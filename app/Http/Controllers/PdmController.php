<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Employee, User};
use Illuminate\Support\Facades\{Auth, DB, Schema};
use Carbon\Carbon;

class PdmController extends Controller
{
    // --- 1. AUTHENTICATION ---
    public function showLoginForm() { 
        return view('employees.login'); 
    }

    public function login(Request $request)
    {
        $request->validate(['employee_no' => 'required', 'password' => 'required']);
        
        $employee = Employee::where('employee_no', $request->employee_no)->first();

        if (!$employee) return back()->withErrors(['msg' => 'Karyawan tidak ditemukan!']);

        // Password default adalah ddmmyyyy dari tanggal lahir
        $dobRaw = $employee->getRawOriginal('dob');
        $defaultPass = $dobRaw ? Carbon::parse($dobRaw)->format('dmY') : null;
        $inputPassMd5 = md5($request->password);

        // Cek password (mendukung plain text ddmmyyyy atau md5 lama)
        if ($request->password !== $defaultPass && $inputPassMd5 !== $employee->password) {
            return back()->withErrors(['msg' => 'Password salah!']);
        }

        // Sinkronisasi ke tabel Users untuk session Auth Laravel
        $user = User::firstOrCreate(
            ['employee_no' => $employee->employee_no],
            [
                'name' => $employee->employee_name,
                'email' => $employee->personal_email ?? $employee->employee_no . '@pdm.com',
                'password' => $employee->password ?? bcrypt($defaultPass),
            ]
        );

        Auth::login($user);
        return redirect()->route('pdm.index');
    }

    public function logout() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }

    // --- 2. USER SIDE: VIEW FORM ---
    public function edit()
    {
        // Mengambil data melalui relasi yang sudah kita buat di model User
        $user = Auth::user();
        $master = Employee::where('employee_no', $user->employee_no)->firstOrFail();
        
        // Ambil riwayat perubahan yang masih 'Pending'
        $pendingLogs = DB::table('employee_update_logs')
                        ->where('employee_no', $user->employee_no)
                        ->where('approval_status', 'Pending')
                        ->get();

        $userData = $master->toArray();

        // Overwrite data master dengan data pending agar muncul di form
        foreach ($pendingLogs as $log) {
            $userData[$log->field_name] = $log->new_value;
        }

        // Format tanggal agar sesuai dengan input type="date"
        $dateFields = ['dob', 'spouse_dob', 'child_1_dob', 'child_2_dob', 'child_3_dob', 
                       'kitas_issued_date', 'kitas_end_date', 'imta_issued_date', 'imta_end_date'];
        foreach ($dateFields as $df) {
            if (!empty($userData[$df])) {
                $userData[$df] = Carbon::parse($userData[$df])->format('Y-m-d');
            }
        }

        $isPending = $pendingLogs->isNotEmpty();
        return view('employees.pdm', compact('userData', 'isPending', 'master'));
    }

    // --- 3. USER SIDE: SUBMIT CHANGES ---
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // 1. Ambil user yang login & validasi data employee
            $user = Auth::user();
            $employee = Employee::where('employee_no', $user->employee_no)->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Profil dengan nomor ' . $user->employee_no . ' tidak ditemukan di database master.'
                ]);
            }

            $employee_no = $employee->employee_no;
            $changes = [];

            // 2. LOGIKA DATA TEKS: Filter input (kecuali token & file)
            $fields = $request->except(['_token', 'fKtp', 'fIjazah', 'fBank', 'fNpwp', 'fKk', 'pernyataan_benar']);

            foreach ($fields as $fieldName => $newValue) {
                if (!Schema::hasColumn('employees', $fieldName)) continue;

                // Ambil nilai mentah dari database (RawOriginal) untuk perbandingan akurat
                $oldValue = $employee->getRawOriginal($fieldName);
                
                $newStr = trim((string)($newValue ?? ''));
                $oldStr = trim((string)($oldValue ?? ''));

                if ($newStr !== $oldStr) {
                    $changes[$fieldName] = [
                        'old' => $oldStr,
                        'new' => $newStr
                    ];
                }
            }

            // 3. LOGIKA FILE UPLOAD: Deteksi file yang diunggah
            $fileFields = [
                'fKtp' => 'ktp', 
                'fIjazah' => 'ijazah', 
                'fBank' => 'bank', 
                'fNpwp' => 'npwp', 
                'fKk' => 'kk'
            ];
            
            foreach ($fileFields as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $newFileName = $file->getClientOriginalName();
                    
                    // Ambil info file lama (jika ada di DB)
                    $oldFile = $employee->getRawOriginal($dbColumn) ?? 'Tidak ada file sebelumnya';

                    $changes[$dbColumn] = [
                        'old' => $oldFile,
                        'new' => '[UPLOAD BARU]: ' . $newFileName
                    ];
                }
            }

            // 4. Validasi: Jika tidak ada perubahan sama sekali (teks maupun file)
            if (empty($changes)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Tidak ada perubahan data yang dideteksi. Data Anda masih sama dengan data di server.'
                ]);
            }

            // 5. A. Simpan Header Request
            $requestId = DB::table('employee_update_requests')->insertGetId([
                'employee_no' => $employee_no,
                'approval_status' => 'Pending',
                'requested_by' => $employee_no,
                'request_ip' => $request->ip(),
                'request_source' => 'Employee',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 6. B. Simpan Detail Log Per Kolom (Looping hasil $changes)
            foreach ($changes as $field => $val) {
                DB::table('employee_update_logs')->insert([
                    'employee_no' => $employee_no,
                    'update_id' => $requestId,
                    'field_name' => $field,
                    'old_value' => $val['old'],
                    'new_value' => $val['new'],
                    'action' => 'Submit',
                    'approval_status' => 'Pending',
                    'acted_by' => $employee_no,
                    'acted_role' => 'employee',
                    'ip_address' => $request->ip(),
                    'created_at' => now()
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Perubahan data dan berkas Anda telah diajukan ke HR untuk disetujui.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Sistem Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- 4. ADMIN SIDE ---
    public function pendingList()
    {
        $requests = DB::table('employee_update_requests')
                      ->join('employees', 'employee_update_requests.employee_no', '=', 'employees.employee_no')
                      ->select('employee_update_requests.*', 'employees.employee_name')
                      ->where('employee_update_requests.approval_status', 'Pending')
                      ->get();

        return view('admin.pdm_approval', compact('requests'));
    }

    public function approveBulk(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $header = DB::table('employee_update_requests')->where('id', $id)->first();
            if (!$header) return back()->with('error', 'Data pengajuan tidak ditemukan.');

            $logs = DB::table('employee_update_logs')->where('update_id', $id)->get();
            $dataToUpdate = [];

            foreach ($logs as $log) {
                $dataToUpdate[$log->field_name] = $log->new_value;
            }

            // Eksekusi update ke tabel utama
            Employee::where('employee_no', $header->employee_no)->update($dataToUpdate);

            // Update status pengajuan
            DB::table('employee_update_requests')->where('id', $id)->update([
                'approval_status' => 'Approved',
                'approved_by' => Auth::user()->employee_no,
                'approved_at' => now()
            ]);

            DB::table('employee_update_logs')->where('update_id', $id)->update([
                'approval_status' => 'Approved',
                'action' => 'Approve',
                'acted_by' => Auth::user()->employee_no,
                'acted_role' => 'hr_admin'
            ]);

            DB::commit();
            return back()->with('success', 'Perubahan data telah disetujui dan diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function getData($employee_no)
    {
        $employee = Employee::where('employee_no', $employee_no)->first();
        
        if ($employee) {
            // Laravel akan otomatis menggunakan format Y-m-d saat array-kan model 
            // karena $casts 'date' di model Employee.
            return response()->json([
                'success' => true,
                'userData' => $employee->toArray() 
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Not found'], 404);
    }
}