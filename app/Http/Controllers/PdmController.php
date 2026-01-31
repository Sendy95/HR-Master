<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PdmController extends Controller
{
    /**
     * Menampilkan Halaman Login
     */
    public function showLoginForm()
    {
        // Mengacu pada resources/views/employees/login.blade.php
        return view('employees.login'); 
    }

    /**
     * 1. PROSES LOGIN (Redirect Mode)
     * Mengarahkan user dari /login ke /pdm jika sukses.
     */
    public function login(Request $request)
    {
        // Validasi input dari form HTML
        $request->validate([
            'employee_no' => 'required',
            'password'    => 'required'
        ]);

        try {
            $employee = Employee::where('employee_no', $request->employee_no)->first();

            if (!$employee) {
                return back()->withErrors(['msg' => 'Nomor Karyawan tidak ditemukan!'])->withInput();
            }

            // Ambil DOB asli untuk password default (ddmmyyyy)
            $dobRaw = $employee->getRawOriginal('dob'); 
            $defaultPass = $dobRaw ? Carbon::parse($dobRaw)->format('dmY') : null;
            $dbPassword = $employee->password;

            $isAuthorized = false;

            // SITUASI 1: Akun Baru (Password DB kosong)
            if (empty($dbPassword)) {
                if ($request->password === $defaultPass) {
                    $isAuthorized = true;
                } else {
                    return back()->withErrors(['msg' => 'Akun belum aktif. Gunakan Tanggal Lahir (DDMMYYYY)!']);
                }
            } 
            // SITUASI 2: Akun Sudah Aktif (Cek MD5)
            else {
                if (md5($request->password) === $dbPassword) {
                    $isAuthorized = true;
                } else {
                    return back()->withErrors(['msg' => 'Password salah!']);
                }
            }

            if ($isAuthorized) {
                // Set Session untuk keamanan rute
                Session::put('isLoggedIn', true);
                Session::put('employee_no', $employee->employee_no);
                Session::put('employee_name', $employee->employee_name);

                return redirect()->route('pdm.index');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    /**
     * 2. AMBIL DATA DETAIL (Read)
     */
    public function getData($employee_no)
    {
        try {
            $employee = Employee::where('employee_no', $employee_no)->first();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'userData' => $this->formatUserData($employee)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 3. UPDATE DATA & UPLOAD FILE (Update)
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $employeeNo = $request->employee_no;
            // Ambil data asli sebagai referensi
            $employee = Employee::where('employee_no', $employeeNo)->first();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan'], 404);
            }

            // 1. PROSES UPLOAD FILE
            $filePaths = [];
            $fileFields = [
                'fKtp'    => 'identity_url',
                'fIjazah' => 'education_certificate_url',
                'fBank'   => 'bank_book_url',
                'fNpwp'   => 'npwp_url',
                'fKk'     => 'family_card_url'
            ];

            foreach ($fileFields as $htmlField => $dbField) {
                if ($request->hasFile($htmlField)) {
                    // Simpan file baru
                    $path = $request->file($htmlField)->store('uploads/pdm/' . $employeeNo, 'public');
                    $filePaths[$dbField] = $path;
                } else {
                    // Gunakan file lama jika tidak ada upload baru
                    $filePaths[$dbField] = $employee->$dbField;
                }
            }

            // 2. LOGIKA IDENTITY EXPIRY (Sinkronisasi dengan JS formData.set)
            // Kita cek apakah input identity_expiry berisi 'Seumur Hidup' atau tanggal valid
            $identityExpiry = $request->identity_expiry;
            if (empty($identityExpiry)) {
                $identityExpiry = 'Seumur Hidup';
            }

            // 3. SIAPKAN DATA UNTUK TABEL employee_updates
            $dataToUpdate = [
                'employee_no'         => $employeeNo,
                'employee_name'       => $request->employee_name,
                'company_name'        => $employee->company_name,
                'status'              => $employee->status,
                'gender'              => $request->gender,
                'pob'                 => $request->pob,
                'dob'                 => $request->dob,
                'blood_type'          => $request->blood_type,
                'religion'            => $request->religion,
                'personal_email'      => $request->personal_email,
                'tribe'               => $request->tribe,
                'phone_1'             => $request->phone_1,
                'phone_1_status'      => $request->phone_1_status,
                'phone_2'             => $request->phone_2,
                'phone_2_status'      => $request->phone_2_status,
                'education_level'     => $request->education_level,
                'bank_account_number' => $request->bank_account_number,
                'npwp_number'         => $request->npwp_number,
                'identity_number'     => $request->identity_number,
                'identity_expiry'     => $identityExpiry, 
                'family_card_number'  => $request->family_card_number,
                'marital_status'      => $request->marital_status,
                'family_status'       => $request->family_status, // Diambil dari input yang dihitung JS
                'ptkp_status'         => $request->ptkp_status,

                // Data Pasangan
                'spouse_name'         => $request->spouse_name,
                'spouse_relation'     => $request->spouse_relation,
                'spouse_dob'          => $request->tanggal_Lahir_pasangan, // Sesuaikan dengan name di HTML

                // Data Anak/Tanggungan (Mapping dari generateDetailTanggungan di JS)
                'child_count'         => $request->child_count ?? 0,
                'child_1_name'        => $request->nama_tanggungan_1,
                'child_1_relation'    => $request->hubungan_tanggungan_1,
                'child_1_dob'         => $request->tanggal_Lahir_tanggungan_1,
                
                'child_2_name'        => $request->nama_tanggungan_2,
                'child_2_relation'    => $request->hubungan_tanggungan_2,
                'child_2_dob'         => $request->tanggal_Lahir_tanggungan_2,
                
                'child_3_name'        => $request->nama_tanggungan_3,
                'child_3_relation'    => $request->hubungan_tanggungan_3,
                'child_3_dob'         => $request->tanggal_Lahir_tanggungan_3,

                'approval_status'     => 'Pending',
                'updated_at'          => now(),
            ];

            // Gabungkan dengan path file
            $dataToUpdate = array_merge($dataToUpdate, $filePaths);

            // 4. SIMPAN/UPDATE KE TABEL employee_updates
            DB::table('employee_updates')->updateOrInsert(
                ['employee_no' => $employeeNo],
                $dataToUpdate
            );

            // 5. CATAT LOG
            DB::table('employee_logs')->insert([
                'employee_no' => $employeeNo,
                'activity'    => 'Pengajuan PDM',
                'details'     => 'Karyawan memperbarui data profil (Status: Pending Approval)',
                'ip_address'  => $request->ip(),
                'created_at'  => now()
            ]);

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Data berhasil diajukan! Perubahan akan muncul setelah diverifikasi oleh HR.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan Halaman Utama PDM dengan Data Karyawan
     */
    public function index()
    {
        // 1. Pastikan Session Login ada
        if (!Session::has('isLoggedIn')) {
            return redirect()->route('login');
        }

        // 2. Ambil No Karyawan dari Session
        $employee_no = Session::get('employee_no');
        
        // 3. Cari data di database
        $employee = Employee::where('employee_no', $employee_no)->first();

        // 4. Jika karyawan tidak ditemukan, logout dan kembali ke login
        if (!$employee) {
            Session::flush();
            return redirect()->route('login')->withErrors(['msg' => 'Data karyawan tidak ditemukan di database.']);
        }

        // 5. Format data (Jika Anda punya fungsi formatUserData, gunakan itu)
        // Jika tidak punya, Anda bisa langsung kirim variabel $employee
        $userData = $this->formatUserData($employee);

        // 6. Tampilkan view dan kirim data
        return view('employees.pdm', compact('userData'));
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }

    /**
     * PRIVATE HELPER: Memformat Data Karyawan untuk JSON
     * Fungsi ini digunakan bersama oleh login() dan getData() agar format data konsisten.
     */
    private function formatUserData($employee)
    {
        return [
            // Data Identitas Utama
            'employee_no'         => $employee->employee_no,
            'employee_name'       => $employee->employee_name,
            'company_name'        => $employee->company_name,
            'status'              => $employee->status,
            'gender'              => $employee->gender,
            'pob'                 => $employee->pob,
            
            // Tanggal Lahir
            'dob'                 => $employee->getRawOriginal('dob'), 
            
            // Data Profil
            'blood_type'          => $employee->blood_type,
            'religion'            => $employee->religion,
            'personal_email'      => $employee->personal_email,
            'tribe'               => $employee->tribe,
            'phone_1'              => $employee->phone_1,
            'phone_1_status'          => $employee->phone_1_status,
            'phone_2'              => $employee->phone_2,
            'phone_2_status'          => $employee->phone_2_status,
            'education_level'           => $employee->education_level,
            
            // Data Dokumen & Legalitas
            'bank_account_number' => $employee->bank_account_number,
            'npwp_number'         => $employee->npwp_number,
            'identity_number'     => $employee->identity_number,
            'identity_expiry'     => $employee->identity_expiry ?? 'Seumur Hidup', // Solusi Error awal
            'family_card_number'  => $employee->family_card_number,
            
            // --- DATA KELUARGA & PAJAK (Tambahan Baru) ---
            'marital_status'      => $employee->marital_status,
            'ptkp_status'          => $employee->ptkp_status, // Sesuai dengan ID ptkp_status di Blade
            'family_status'          => $employee->family_status, // Status pernikahan dan tanggungan
            'has_children'      => $employee->child_count > 0, // Boolean untuk checkbox Punya tanggungan
            'child_count'    => $employee->child_count ?? 0,
            
            // Data Pasangan
            'spouse_name'         => $employee->spouse_name,
            'spouse_relation'     => $employee->spouse_relation, // Istri/Suami
            'spouse_dob'   => $employee->spouse_dob,
            
            // Data Anak (Mapping ke array untuk JavaScript existingChildren)
            'children_details'    => [
                ['name' => $employee->child_1_name, 'relation' => $employee->child_1_relation, 'birth_date' => $employee->child_1_dob],
                ['name' => $employee->child_2_name, 'relation' => $employee->child_2_relation, 'birth_date' => $employee->child_2_dob],
                ['name' => $employee->child_3_name, 'relation' => $employee->child_3_relation, 'birth_date' => $employee->child_3_dob],
            ],

            // Mapping URLs File
            'fileURLs'            => [
                'ktp'    => !empty($employee->identity_url) ? asset('storage/' . $employee->identity_url) : null,
                'ijazah' => !empty($employee->education_certificate_url) ? asset('storage/' . $employee->education_certificate_url) : null,
                'bank'   => !empty($employee->bank_book_url) ? asset('storage/' . $employee->bank_book_url) : null,
                'npwp'   => !empty($employee->npwp_url) ? asset('storage/' . $employee->npwp_url) : null,
                'kk'     => !empty($employee->family_card_url) ? asset('storage/' . $employee->family_card_url) : null,
            ]
        ];
    }

    /**
     * PRIVATE HELPER: Masking Email (ex: budi@gmail.com -> bud***@gmail.com)
     */
    private function maskEmail($personal_email) 
    {
        if (!$personal_email || !str_contains($personal_email, "@")) return "Email tidak terdaftar";
        $parts = explode("@", $personal_email);
        return substr($parts[0], 0, 3) . "****@" . $parts[1];
    }

    /**
     * TAMPILAN: Daftar Approval (HR)
     */
public function pendingList(Request $request) {
    if (!Session::has('isLoggedIn')) return redirect()->route('login');

    // Jika ada ID di URL, tampilkan halaman DETAIL per karyawan
    if ($request->has('id')) {
        $update = DB::table('employee_updates')->where('id', $request->id)->first();
        $master = DB::table('employees')->where('employee_no', $update->employee_no)->first();

        return view('admin.pdm_detail', compact('update', 'master'));
    }

    // Jika tidak ada ID, tampilkan halaman DAFTAR (INDEX)
    $approvals = DB::table('employee_updates as u')
        ->join('employees as e', 'u.employee_no', '=', 'e.employee_no')
        ->select('u.id', 'u.employee_no', 'e.employee_name', 'u.created_at', 'u.approval_status')
        ->where('u.approval_status', 'Pending')
        ->orderBy('u.created_at', 'asc')
        ->get();

    return view('admin.pdm_approval', ['requests' => $approvals]);
}

    /**
     * PROSES: Eksekusi Approval/Reject (HR Admin)
     */
    public function approveAction(Request $request, $id) {
        try {
            DB::beginTransaction();
            
            $updateData = DB::table('employee_updates')->where('id', $id)->first();
            if (!$updateData) return back()->with('error', 'Data tidak ditemukan.');

            if ($request->action == 'approve') {
                $dataToEmployees = (array)$updateData;
                
                // Hapus key yang tidak ada di tabel employees atau bersifat internal tabel update
                unset(
                    $dataToEmployees['id'], 
                    $dataToEmployees['approval_status'], 
                    $dataToEmployees['hr_note'],
                    $dataToEmployees['created_at'], 
                    $dataToEmployees['updated_at'],
                    $dataToEmployees['old_name'], 
                    $dataToEmployees['old_marital'], 
                    $dataToEmployees['old_tax']
                );
                
                // Update Tabel Utama
                DB::table('employees')->where('employee_no', $updateData->employee_no)->update($dataToEmployees);
                
                // Update Status Tabel Pengajuan
                DB::table('employee_updates')->where('id', $id)->update(['approval_status' => 'Approved']);
                $msg = "Data berhasil disetujui dan diperbarui.";
            } else {
                DB::table('employee_updates')->where('id', $id)->update(['approval_status' => 'Rejected']);
                $msg = "Pengajuan telah ditolak.";
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}