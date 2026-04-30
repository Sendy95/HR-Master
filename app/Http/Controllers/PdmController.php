<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Employee, User, Employeedocument};
use Illuminate\Support\Facades\{Auth, DB, Schema, Storage, Hash, Mail, Log}; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\OtpMail;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class PdmController extends Controller
{
    // --- 1. AUTHENTICATION & OTP ---

    public function showLoginForm() { 
        return view('employees.login'); 
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_no' => 'required',
            'password' => 'required'
        ]);
        
        $employee = Employee::where('employee_no', $request->employee_no)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan!'], 404);
        }

        // Password default: format dmY (misal 16091995)
        $dobRaw = $employee->getRawOriginal('dob'); 
        $defaultPass = $dobRaw ? Carbon::parse($dobRaw)->format('dmY') : null;
        
        $user = User::where('employee_no', $employee->employee_no)->first();

        // Cek apakah user sudah pernah ganti password sebelumnya
        $hasChangedPassword = $user && !empty($user->password_changed_at);
        $isPasswordCorrect = false;

        if ($hasChangedPassword) {
            // JIKA SUDAH GANTI: Hanya boleh login menggunakan password yang tersimpan (Hash)
            if (Hash::check($request->password, $user->password)) {
                $isPasswordCorrect = true;
            }
        } else {
            // JIKA BELUM GANTI (Login Pertama): Cek password default atau MD5 lama
            if ($request->password === $defaultPass) {
                $isPasswordCorrect = true;
            } elseif ($user && Hash::check($request->password, $user->password)) {
                $isPasswordCorrect = true;
            }
        }

        if (!$isPasswordCorrect) {
            return response()->json([
                'success' => false,
                'message_key' => 'err_wrong_password' // Selalu kirim key yang sama
            ]);
        }

        // Jika user belum ada di tabel users (baru pertama kali login sama sekali), buatkan otomatis
        if (!$user) {
            $user = User::create([
                'employee_no' => $employee->employee_no,
                'name' => $employee->employee_name,
                'email' => $employee->personal_email ?? ($employee->employee_no . '@pdm.com'),
                'password' => Hash::make($request->password),
            ]);
        }

        // Login User ke Session
        Auth::login($user);
        
        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Penanda untuk Frontend agar menampilkan modal ganti password
        $isFirstLogin = empty($user->password_changed_at);

        return response()->json([
            'success' => true,
            'force_password_change' => $isFirstLogin,
            'employee_no' => $user->employee_no,
            'email' => $user->email,
            'new_csrf_token' => csrf_token(),
            'message' => 'Login Berhasil'
        ]);
    }

private function generateAndSendOtp($user) {
        // 1. Generate 6 digit angka
        $otp = rand(100000, 999999);
        
        // 2. Simpan ke database dengan masa berlaku 5 menit
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5)
        ]);

        // 3. Ambil preferensi bahasa dari request frontend
        $lang = request()->lang ?? 'id';

        try {
            // Log untuk debug: Memastikan company_name dari tabel employees terbaca
            Log::info("Mengirim OTP. User: {$user->employee_no}, Company: {$user->company_name}, Lang: {$lang}");

            /**
             * 4. Kirim Email
             * Kita mengirimkan objek $user hasil JOIN yang sudah memiliki properti 'company_name'
             */
            Mail::to($user->email)->send(new OtpMail($otp, $user, $lang));
            
            return $otp;
        } catch (\Exception $e) {
            Log::error("Gagal kirim email OTP ke {$user->email}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fungsi Public untuk Resend OTP (API Endpoint)
     */
    public function resendOtp(Request $request) {
        $request->validate([
            'employee_no' => 'required',
            'lang'        => 'nullable|string' // Menangkap bahasa dari frontend
        ]);

        $lang = $request->lang ?? 'id';

        /**
         * 1. Ambil data User JOIN dengan Employees
         * Karena company_name ada di tabel employees, kita wajib join berdasarkan user_id.
         */
        $user = User::join('employees', 'users.id', '=', 'employees.user_id')
                    ->where('users.employee_no', $request->employee_no)
                    ->select(
                        'users.*', 
                        'employees.company_name' // Kolom krusial untuk OtpMail
                    )
                    ->first();
        
        if ($user) {
            $otpStatus = $this->generateAndSendOtp($user);
            
            if ($otpStatus) {
                // Response pesan sukses sesuai bahasa
                $msg = ($lang === 'en') 
                    ? 'A new OTP has been sent to your email.' 
                    : 'OTP baru telah dikirim ke email.';

                return response()->json([
                    'success' => true, 
                    'message' => $msg
                ]);
            }

            // Response jika server email bermasalah
            $errorMsg = ($lang === 'en') ? 'Failed to send email.' : 'Gagal mengirim email.';
            return response()->json(['success' => false, 'message' => $errorMsg], 500);
        }
        
        // Response jika nomor karyawan tidak terdaftar
        $notFoundMsg = ($lang === 'en') ? 'User not found.' : 'User tidak ditemukan.';
        return response()->json(['success' => false, 'message' => $notFoundMsg], 404);
    }
    
    public function logout() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }

    // --- 2. UPDATE PASSWORD PERTAMA ---

    public function updatePasswordFirst(Request $request)
    {
        $request->validate([
            'dob' => 'required',
            'otp' => 'required',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[0-9]/|regex:/[^A-Za-z0-9]/'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi berakhir, silakan login ulang.'], 401);
        }

        $employee = Employee::where('employee_no', $user->employee_no)->first();

        // 1. Verifikasi Tanggal Lahir (dmY)
        $dobMaster = Carbon::parse($employee->getRawOriginal('dob'))->format('dmY');
        if ($request->dob !== $dobMaster) {
            return response()->json(['success' => false, 'message' => 'Tanggal lahir tidak cocok!'], 422);
        }

        // 2. Verifikasi OTP
        if ($request->otp != $user->otp_code) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 422);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kadaluwarsa!'], 422);
        }

        // 3. Simpan password baru
        try {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = now(); 
            $user->otp_code = null; 
            $user->otp_expires_at = null;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui!']);
        } catch (\Exception $e) {
            Log::error("Gagal update password user {$user->employee_no}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data ke server.'], 500);
        }
    }

    // --- 3. PDM LOGIC (DATA KARYAWAN) ---

    public function edit()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $master = Employee::where('employee_no', $user->employee_no)->firstOrFail();
        
        $pendingLogs = DB::table('employee_update_logs')
                        ->where('employee_no', $user->employee_no)
                        ->where('approval_status', 'Pending')
                        ->get();

        $userData = $master->getAttributes();

        foreach ($pendingLogs as $log) {
            $userData[$log->field_name] = $log->new_value;
        }

        $dateFields = ['dob', 'spouse_dob', 'child_1_dob', 'child_2_dob', 'child_3_dob', 'identity_expiry'];
        foreach ($dateFields as $df) {
            if (isset($userData[$df]) && !empty($userData[$df]) && $userData[$df] !== 'Seumur Hidup') {
                try { $userData[$df] = Carbon::parse($userData[$df])->format('Y-m-d'); } catch (\Exception $e) {}
            }
        }

        $uploadCounts = $this->getLocalUploadCounts($master->id);
        $isPending = $pendingLogs->isNotEmpty();
        $logDataJson = json_encode($userData);
        
        return view('employees.pdm', compact('userData', 'isPending', 'master', 'logDataJson', 'uploadCounts'));
    }

    private function getLocalUploadCounts($employeeId) {
        $types = ['ktp', 'ijazah', 'bank', 'npwp', 'kk'];
        $counts = [];
        foreach ($types as $type) {
            $counts[$type] = DB::table('employee_documents')
                                ->where('employee_id', $employeeId)
                                ->where('document_type', $type)
                                ->count();
        }
        return $counts;
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $employee = Employee::where('employee_no', $user->employee_no)->first();
            
            if (!$employee) return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.']);

            $latestPendingLogs = DB::table('employee_update_logs')
                ->where('employee_no', $user->employee_no)
                ->where('approval_status', 'Pending')
                ->whereIn('id', function($query) use ($user) {
                    $query->select(DB::raw('MAX(id)'))->from('employee_update_logs')
                          ->where('employee_no', $user->employee_no)
                          ->where('approval_status', 'Pending')->groupBy('field_name');
                })->get()->keyBy('field_name');

            $masterData = $employee->getAttributes();
            $changes = [];
            $fileFields = ['identity_url'=>'ktp', 'education_certificate_url'=>'ijazah', 'bank_book_url'=>'bank', 'npwp_url'=>'npwp', 'family_card_url'=>'kk'];
            
            $inputData = $request->except(array_merge(['_token', 'pernyataan_benar', 'identity_expiry_forever'], array_keys($fileFields)));
            if ($request->has('identity_expiry_forever')) $inputData['identity_expiry'] = 'Seumur Hidup';

            foreach ($inputData as $fieldName => $newValue) {
                if (!Schema::hasColumn('employees', $fieldName)) continue;

                $comparisonValue = $latestPendingLogs->has($fieldName) ? $latestPendingLogs[$fieldName]->new_value : ($masterData[$fieldName] ?? '');
                
                $newStr = trim((string)$newValue);
                $compStr = trim((string)$comparisonValue);

                if ($newStr === $compStr) continue; 

                $changes[$fieldName] = ['old' => $masterData[$fieldName] ?? '', 'new' => $newStr];
            }

            // --- Bagian Handle File Uploads dengan Logika Rename Folder Berbasis Employee No ---
            foreach ($fileFields as $inputName => $docType) {
                if ($request->hasFile($inputName)) {
                    // 1. Ambil Nama Karyawan Baru & Format Safe Name
                    $currentName = $request->input('employee_name') ?? $employee->employee_name;
                    $safeName = str_replace(' ', '_', Str::headline($currentName));
                    $newFolderName = "{$employee->employee_no}_{$safeName}";

                    // 2. Tentukan format case Dokumen
                    if (in_array($docType, ['ktp', 'kk', 'npwp'])) {
                        $formattedDocType = strtoupper($docType);
                    } else {
                        $formattedDocType = ucfirst($docType);
                    }

                    // 3. LOGIKA RENAME: Cari folder yang diawali dengan employee_no
                    $basePath = 'documents';
                    $allFolders = Storage::disk('public')->directories($basePath);
                    
                    $oldFolderPath = null;
                    foreach ($allFolders as $folder) {
                        // Cek apakah folder dimulai dengan "15021649_"
                        if (str_starts_with(basename($folder), $employee->employee_no . '_')) {
                            $oldFolderPath = $folder;
                            break;
                        }
                    }

                    // 4. Eksekusi Rename Folder jika ditemukan dan berbeda dengan nama baru
                    if ($oldFolderPath && basename($oldFolderPath) !== $newFolderName) {
                        Storage::disk('public')->move($oldFolderPath, "{$basePath}/{$newFolderName}");
                    } elseif (!$oldFolderPath) {
                        // Jika folder sama sekali belum ada, buat baru
                        Storage::disk('public')->makeDirectory("{$basePath}/{$newFolderName}/{$formattedDocType}");
                    }

                    // 5. Hitung Kuota & Simpan File
                    $currentCount = DB::table('employee_documents')
                        ->where('employee_id', $employee->id)
                        ->where('document_type', $docType)
                        ->count();

                    if ($currentCount >= 2) {
                        return response()->json(['success' => false, 'message' => "Kuota upload " . $formattedDocType . " penuh."], 400);
                    }

                    $file = $request->file($inputName);
                    $fileName = "{$employee->employee_no}_{$safeName}_{$formattedDocType}_" . ($currentCount + 1) . "." . $file->getClientOriginalExtension();
                    
                    $newPath = "{$basePath}/{$newFolderName}/{$formattedDocType}";
                    $path = $file->storeAs($newPath, $fileName, 'public');

                    // 6. Update Database
                    DB::table('employee_documents')->insert([
                        'employee_id' => $employee->id, 
                        'document_type' => $docType, 
                        'file_path' => '/storage/' . $path,
                        'upload_count' => $currentCount + 1, 
                        'created_at' => now(), 
                        'updated_at' => now()
                    ]);

                    $changes[$inputName] = ['old' => 'File Lama', 'new' => 'Upload Baru: ' . $fileName];
                }
            }
            
            // Ambil bahasa dari request yang dikirim JS, default ke 'id'
            $lang = $request->input('selectedLang', 'id'); 

            if (empty($changes)) {
                // Tentukan pesan berdasarkan bahasa
                $message = ($lang === 'en') 
                    ? 'The data you entered is the same as before. No changes were saved.' 
                    : 'Data yang Anda masukkan sama dengan sebelumnya. Tidak ada perubahan yang disimpan.';

                return response()->json([
                    'success' => false, 
                    'message' => $message 
                ], 422);
            }

            $requestId = DB::table('employee_update_requests')->insertGetId([
                'employee_no' => $employee->employee_no, 'approval_status' => 'Pending',
                'requested_by' => $employee->employee_no, 'request_ip' => $request->ip(),
                'request_source' => 'Employee', 'created_at' => now(), 'updated_at' => now()
            ]);

            foreach ($changes as $field => $val) {
                DB::table('employee_update_logs')->insert([
                    'employee_no' => $employee->employee_no, 'update_id' => $requestId, 'field_name' => $field,
                    'old_value' => $val['old'], 'new_value' => $val['new'], 'action' => 'Submit',
                    'approval_status' => 'Pending', 'acted_by' => $employee->employee_no, 'acted_role' => 'employee',
                    'ip_address' => $request->ip(), 'created_at' => now()
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dikirim!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // --- 4. ADMIN SIDE & DOCS ---

    public function pendingList() {
        $requests = DB::table('employee_update_requests')
                    ->join('employees', 'employee_update_requests.employee_no', '=', 'employees.employee_no')
                    ->select('employee_update_requests.*', 'employees.employee_name')
                    ->where('employee_update_requests.approval_status', 'Pending')->get();
        return view('admin.pdm_approval', compact('requests'));
    }

    public function approveBulk(Request $request, $id) {
        try {
            DB::beginTransaction();
            $header = DB::table('employee_update_requests')->where('id', $id)->first();
            $logs = DB::table('employee_update_logs')->where('update_id', $id)->get();
            $dataToUpdate = [];

            foreach ($logs as $log) {
                $docFields = ['identity_url', 'family_card_url', 'npwp_url', 'bank_book_url', 'education_certificate_url'];
                if (!in_array($log->field_name, $docFields)) $dataToUpdate[$log->field_name] = $log->new_value;
            }

            if (!empty($dataToUpdate)) Employee::where('employee_no', $header->employee_no)->update($dataToUpdate);

            DB::table('employee_update_requests')->where('id', $id)->update(['approval_status' => 'Approved', 'approved_by' => Auth::user()->employee_no, 'approved_at' => now()]);
            DB::table('employee_update_logs')->where('update_id', $id)->update(['approval_status' => 'Approved']);

            DB::commit();
            return back()->with('success', 'Data diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewDocument($type) {
        $user = Auth::user();
        if (!$user) abort(401);
        
        $employee = Employee::where('employee_no', $user->employee_no)->firstOrFail();
        $document = DB::table('employee_documents')->where('employee_id', $employee->id)->where('document_type', $type)->orderBy('upload_count', 'desc')->first();
        if (!$document) abort(404, "Berkas tidak ada.");
        return redirect(asset($document->file_path));
    }
    
    // Tambahkan di dalam class PdmController
    public function getUserEmail(Request $request)
    {
        // Gunakan pesan error umum dari Laravel jika validasi gagal
        $request->validate(['employee_no' => 'required']);

        // Sesuaikan 'users' dengan nama tabel Anda
        $user = \DB::table('users')
                    ->where('employee_no', $request->employee_no)
                    ->first();

        if ($user && $user->email) {
            return response()->json([
                'success' => true,
                'email' => $user->email
            ]);
        }

        // Mengirimkan message_key agar dikenali oleh translations.js di frontend
        return response()->json([
            'success' => false, 
            'message_key' => 'err_user_not_found', // Key untuk kamus
            'message' => 'Nomor Karyawan tidak terdaftar.' // Fallback teks asli
        ], 404);
    }

    public function showReport()
    {
        $allowedRoles = ['super_admin', 'hr_manager', 'hr_generalist'];
        if (!Auth::check() || !in_array(Auth::user()->role, $allowedRoles)) {
            abort(403);
        }

        // 2. Query SQL (Tetap menggunakan query original Anda)
        $sql = "
            SELECT 
                e.employee_no, e.employee_name, e.company_name, e.status, e.gender, 
                e.pob, e.dob, e.blood_type, e.religion, e.personal_email, e.tribe, 
                e.phone_1, e.phone_1_status, e.phone_2, e.phone_2_status,
                COALESCE(upd.new_education_level, e.education_level) AS education_level,
                doc_ijazah.file_path AS ijazah_file,
                COALESCE(upd.new_bank_account_number, e.bank_account_number) AS bank_account_number,
                doc_bank.file_path AS bank_book_file,
                COALESCE(upd.new_npwp_number, e.npwp_number) AS npwp_number,
                doc_npwp.file_path AS npwp_file,
                COALESCE(upd.new_identity_number, e.identity_number) AS identity_number,
                COALESCE(upd.new_identity_expiry, e.identity_expiry) AS identity_expiry,
                doc_ktp.file_path AS ktp_file,
                COALESCE(upd.new_family_card_number, e.family_card_number) AS family_card_number,
                doc_kk.file_path AS family_card_file,
                e.marital_status, 
                COALESCE(upd.new_family_status, e.family_status) AS family_status, 
                COALESCE(upd.new_spouse_relation, e.spouse_relation) AS spouse_relation, 
                COALESCE(upd.new_ptkp_status, e.ptkp_status) AS ptkp_status,
                e.spouse_name, e.spouse_dob, e.child_count, 
                e.child_1_name, e.child_1_relation, e.child_1_dob, 
                e.child_2_name, e.child_2_relation, e.child_2_dob, 
                e.child_3_name, e.child_3_relation, e.child_3_dob,
                last_log.created_at AS last_update_time
            FROM employees e
            LEFT JOIN (
                SELECT 
                    employee_no,
                    MAX(CASE WHEN field_name = 'education_level' THEN new_value END) AS new_education_level,
                    MAX(CASE WHEN field_name = 'bank_account_number' THEN new_value END) AS new_bank_account_number,
                    MAX(CASE WHEN field_name = 'npwp_number' THEN new_value END) AS new_npwp_number,
                    MAX(CASE WHEN field_name = 'identity_number' THEN new_value END) AS new_identity_number,
                    MAX(CASE WHEN field_name = 'identity_expiry' THEN new_value END) AS new_identity_expiry,
                    MAX(CASE WHEN field_name = 'family_card_number' THEN new_value END) AS new_family_card_number,
                    MAX(CASE WHEN field_name = 'family_status' THEN new_value END) AS new_family_status,
                    MAX(CASE WHEN field_name = 'spouse_relation' THEN new_value END) AS new_spouse_relation,
                    MAX(CASE WHEN field_name = 'ptkp_status' THEN new_value END) AS new_ptkp_status
                FROM employee_update_logs
                WHERE id IN (SELECT MAX(id) FROM employee_update_logs GROUP BY employee_no, field_name)
                GROUP BY employee_no
            ) AS upd ON e.employee_no = upd.employee_no
            LEFT JOIN employee_documents doc_ijazah ON e.id = doc_ijazah.employee_id AND doc_ijazah.document_type = 'ijazah'
            LEFT JOIN employee_documents doc_bank ON e.id = doc_bank.employee_id AND doc_bank.document_type = 'bank'
            LEFT JOIN employee_documents doc_npwp ON e.id = doc_npwp.employee_id AND doc_npwp.document_type = 'npwp'
            LEFT JOIN employee_documents doc_ktp ON e.id = doc_ktp.employee_id AND doc_ktp.document_type = 'ktp'
            LEFT JOIN employee_documents doc_kk ON e.id = doc_kk.employee_id AND doc_kk.document_type = 'kk'
            LEFT JOIN (
                SELECT employee_no, approval_status, created_at
                FROM employee_update_logs
                WHERE id IN (SELECT MAX(id) FROM employee_update_logs GROUP BY employee_no)
            ) AS last_log ON e.employee_no = last_log.employee_no
        ";

        $data = DB::select($sql);
        
        return view('exports.employee_report', compact('data'));
    }

    public function export()
    {
        // 1. Proteksi Keamanan Sisi Server
        $allowedRoles = ['super_admin', 'hr_manager', 'hr_generalist'];
        if (!Auth::check() || !in_array(Auth::user()->role, $allowedRoles)) {
            abort(403);
        }

        // 2. Query SQL (Tetap menggunakan query original Anda)
        $sql = "
            SELECT 
                e.employee_no, e.employee_name, e.company_name, e.status, e.gender, 
                e.pob, e.dob, e.blood_type, e.religion, e.personal_email, e.tribe, 
                e.phone_1, e.phone_1_status, e.phone_2, e.phone_2_status,
                COALESCE(upd.new_education_level, e.education_level) AS education_level,
                doc_ijazah.file_path AS ijazah_file,
                COALESCE(upd.new_bank_account_number, e.bank_account_number) AS bank_account_number,
                doc_bank.file_path AS bank_book_file,
                COALESCE(upd.new_npwp_number, e.npwp_number) AS npwp_number,
                doc_npwp.file_path AS npwp_file,
                COALESCE(upd.new_identity_number, e.identity_number) AS identity_number,
                COALESCE(upd.new_identity_expiry, e.identity_expiry) AS identity_expiry,
                doc_ktp.file_path AS ktp_file,
                COALESCE(upd.new_family_card_number, e.family_card_number) AS family_card_number,
                doc_kk.file_path AS family_card_file,
                e.marital_status, 
                COALESCE(upd.new_family_status, e.family_status) AS family_status, 
                COALESCE(upd.new_spouse_relation, e.spouse_relation) AS spouse_relation, 
                COALESCE(upd.new_ptkp_status, e.ptkp_status) AS ptkp_status,
                e.spouse_name, e.spouse_dob, e.child_count, 
                e.child_1_name, e.child_1_relation, e.child_1_dob, 
                e.child_2_name, e.child_2_relation, e.child_2_dob, 
                e.child_3_name, e.child_3_relation, e.child_3_dob,
                last_log.created_at AS last_update_time
            FROM employees e
            LEFT JOIN (
                SELECT 
                    employee_no,
                    MAX(CASE WHEN field_name = 'education_level' THEN new_value END) AS new_education_level,
                    MAX(CASE WHEN field_name = 'bank_account_number' THEN new_value END) AS new_bank_account_number,
                    MAX(CASE WHEN field_name = 'npwp_number' THEN new_value END) AS new_npwp_number,
                    MAX(CASE WHEN field_name = 'identity_number' THEN new_value END) AS new_identity_number,
                    MAX(CASE WHEN field_name = 'identity_expiry' THEN new_value END) AS new_identity_expiry,
                    MAX(CASE WHEN field_name = 'family_card_number' THEN new_value END) AS new_family_card_number,
                    MAX(CASE WHEN field_name = 'family_status' THEN new_value END) AS new_family_status,
                    MAX(CASE WHEN field_name = 'spouse_relation' THEN new_value END) AS new_spouse_relation,
                    MAX(CASE WHEN field_name = 'ptkp_status' THEN new_value END) AS new_ptkp_status
                FROM employee_update_logs
                WHERE id IN (SELECT MAX(id) FROM employee_update_logs GROUP BY employee_no, field_name)
                GROUP BY employee_no
            ) AS upd ON e.employee_no = upd.employee_no
            LEFT JOIN employee_documents doc_ijazah ON e.id = doc_ijazah.employee_id AND doc_ijazah.document_type = 'ijazah'
            LEFT JOIN employee_documents doc_bank ON e.id = doc_bank.employee_id AND doc_bank.document_type = 'bank'
            LEFT JOIN employee_documents doc_npwp ON e.id = doc_npwp.employee_id AND doc_npwp.document_type = 'npwp'
            LEFT JOIN employee_documents doc_ktp ON e.id = doc_ktp.employee_id AND doc_ktp.document_type = 'ktp'
            LEFT JOIN employee_documents doc_kk ON e.id = doc_kk.employee_id AND doc_kk.document_type = 'kk'
            LEFT JOIN (
                SELECT employee_no, approval_status, created_at
                FROM employee_update_logs
                WHERE id IN (SELECT MAX(id) FROM employee_update_logs GROUP BY employee_no)
            ) AS last_log ON e.employee_no = last_log.employee_no
        ";

        $data = DB::select($sql);

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\EmployeeExport($data), 'Data_Karyawan_Master.xlsx');
    }
}
