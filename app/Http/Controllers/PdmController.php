<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Employee, User};
use Illuminate\Support\Facades\{Auth, DB, Schema, Http};
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

        $dobRaw = $employee->getRawOriginal('dob');
        $defaultPass = $dobRaw ? Carbon::parse($dobRaw)->format('dmY') : null;
        $inputPassMd5 = md5($request->password);

        if ($request->password !== $defaultPass && $inputPassMd5 !== $employee->password) {
            return back()->withErrors(['msg' => 'Password salah!']);
        }

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
        $user = Auth::user();
        $master = Employee::where('employee_no', $user->employee_no)->firstOrFail();
        
        $pendingLogs = DB::table('employee_update_logs')
                        ->where('employee_no', $user->employee_no)
                        ->where('approval_status', 'Pending')
                        ->get();

        $userData = $master->getAttributes();

        foreach ($pendingLogs as $log) {
            $userData[$log->field_name] = $log->new_value;
        }

        // Sinkronisasi data anak
        $maxChildFound = 0;
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($userData["child_{$i}_name"])) {
                $maxChildFound = $i;
            }
        }
        if ($maxChildFound > ($userData['child_count'] ?? 0)) {
            $userData['child_count'] = $maxChildFound;
        }
        $userData['has_children'] = (($userData['child_count'] ?? 0) > 0);

        // Format tanggal agar sesuai input date
        $dateFields = ['dob', 'spouse_dob', 'child_1_dob', 'child_2_dob', 'child_3_dob', 'identity_expiry'];
        foreach ($dateFields as $df) {
            if (isset($userData[$df]) && !empty($userData[$df]) && $userData[$df] !== 'Seumur Hidup') {
                try {
                    $userData[$df] = \Carbon\Carbon::parse($userData[$df])->format('Y-m-d');
                } catch (\Exception $e) {}
            }
        }

        // INTEGRASI GOOGLE DRIVE: Ambil jumlah file
        $gasUrl = "https://script.google.com/macros/s/AKfycbzOgw5Z5b3Imdxp8p1Y_yn0czeOchCF4ngo8MytYSGAyangWAKQa2Q9ugNsImzw6aFC/exec";
        
        // Key disesuaikan dengan kebutuhan mapping JavaScript di View
        $uploadCounts = [
            'ktp'    => 0, 
            'ijazah' => 0, 
            'bank'   => 0, 
            'npwp'   => 0, 
            'kk'     => 0
        ];

        try {
            $response = Http::timeout(10)->get($gasUrl, [
                'action' => 'getCounts',
                'noKaryawan' => $user->employee_no
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['counts'])) {
                    // Mapping dari bahasa manusia (GAS) ke key sistem (JS)
                    $gasCounts = $data['counts'];
                    $uploadCounts['ktp']    = $gasCounts['ktp'] ?? 0;
                    $uploadCounts['ijazah'] = $gasCounts['ijazah'] ?? 0;
                    $uploadCounts['bank']   = $gasCounts['bank'] ?? 0;
                    $uploadCounts['npwp']   = $gasCounts['npwp'] ?? 0;
                    $uploadCounts['kk']     = $gasCounts['kk'] ?? 0;
                }
            }
        } catch (\Exception $e) {}

        $isPending = $pendingLogs->isNotEmpty();
        $logDataJson = json_encode($userData);
        
        return view('employees.pdm', compact('userData', 'isPending', 'master', 'logDataJson', 'uploadCounts'));
    }

    // --- 3. USER SIDE: SUBMIT CHANGES ---
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            $employee = Employee::where('employee_no', $user->employee_no)->first();
            
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.']);
            }

            $employee_no = $employee->employee_no;
            $employee_name = $employee->employee_name;
            $changes = [];

            // 1. Mapping File: SINKRON DENGAN HTML NAME & ID BARU
            $fileMapping = [
                'education_certificate_url' => ['key' => 'fileIjazah', 'col' => 'education_certificate_url'],
                'bank_book_url'             => ['key' => 'fileBank',   'col' => 'bank_book_url'],
                'npwp_url'                  => ['key' => 'fileNpwp',   'col' => 'npwp_url'],
                'identity_url'              => ['key' => 'fileKtp',    'col' => 'identity_url'],
                'family_card_url'           => ['key' => 'fileKk',     'col' => 'family_card_url'],
            ];

            // Filter input teks biasa
            $excludedFields = array_merge(
                ['_token', 'pernyataan_benar', 'identity_expiry_forever', 'has_children'],
                array_keys($fileMapping)
            );
            
            $fields = $request->except($excludedFields);

            if ($request->identity_expiry_forever === 'on' || $request->has('identity_expiry_forever')) {
                $fields['identity_expiry'] = 'Seumur Hidup';
            }

            foreach ($fields as $fieldName => $newValue) {
                if (!Schema::hasColumn('employees', $fieldName)) continue;

                $oldValue = $employee->getRawOriginal($fieldName);
                $newStr = trim((string)($newValue ?? ''));
                $oldStr = trim((string)($oldValue ?? ''));

                $dateFields = ['dob', 'spouse_dob', 'child_1_dob', 'child_2_dob', 'child_3_dob', 'identity_expiry'];
                if (in_array($fieldName, $dateFields) && $newStr !== 'Seumur Hidup' && $newStr !== '') {
                    $newStr = date('Y-m-d', strtotime($newStr));
                    $oldStr = $oldStr ? date('Y-m-d', strtotime($oldStr)) : '';
                }

                if ($newStr !== $oldStr) {
                    $changes[$fieldName] = ['old' => $oldStr, 'new' => $newStr];
                }
            }

            // 2. LOGIKA UPLOAD KE GOOGLE DRIVE
            $gasUrl = "https://script.google.com/macros/s/AKfycbzOgw5Z5b3Imdxp8p1Y_yn0czeOchCF4ngo8MytYSGAyangWAKQa2Q9ugNsImzw6aFC/exec";
            
            $googlePayload = [
                'noKaryawan' => $employee_no,
                'namaKaryawan' => $employee_name,
                'fileIjazah' => null, 'fileBank' => null, 'fileNpwp' => null, 'fileKtp' => null, 'fileKk' => null
            ];

            $hasFiles = false;
            foreach ($fileMapping as $inputName => $map) {
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $googlePayload[$map['key']] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
                    $hasFiles = true;
                }
            }

            if ($hasFiles) {
                $response = Http::timeout(60)->post($gasUrl, $googlePayload);
                if ($response->successful() && $response->json('success')) {
                    $urls = $response->json('data');
                    // Simpan URL baru ke perubahan logs
                    if (!empty($urls['urlIjazah'])) $changes['education_certificate_url'] = ['old' => $employee->education_certificate_url, 'new' => $urls['urlIjazah']];
                    if (!empty($urls['urlBank']))   $changes['bank_book_url'] = ['old' => $employee->bank_book_url, 'new' => $urls['urlBank']];
                    if (!empty($urls['urlNpwp']))   $changes['npwp_url'] = ['old' => $employee->npwp_url, 'new' => $urls['urlNpwp']];
                    if (!empty($urls['urlKtp']))    $changes['identity_url'] = ['old' => $employee->identity_url, 'new' => $urls['urlKtp']];
                    if (!empty($urls['urlKk']))     $changes['family_card_url'] = ['old' => $employee->family_card_url, 'new' => $urls['urlKk']];
                } else {
                    throw new \Exception("Gagal upload berkas ke Drive: " . ($response->json('message') ?? 'Unknown Error'));
                }
            }

            if (empty($changes)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada perubahan data.']);
            }

            // 3. Simpan ke Logs
            $requestId = DB::table('employee_update_requests')->insertGetId([
                'employee_no' => $employee_no,
                'approval_status' => 'Pending',
                'requested_by' => $employee_no,
                'request_ip' => $request->ip(),
                'request_source' => 'Employee',
                'created_at' => now(), 'updated_at' => now()
            ]);

            foreach ($changes as $field => $val) {
                DB::table('employee_update_logs')->insert([
                    'employee_no' => $employee_no,
                    'update_id' => $requestId,
                    'field_name' => $field,
                    'old_value' => $val['old'] ?? '',
                    'new_value' => $val['new'] ?? '',
                    'action' => 'Submit',
                    'approval_status' => 'Pending',
                    'acted_by' => $employee_no,
                    'acted_role' => 'employee',
                    'ip_address' => $request->ip(),
                    'created_at' => now()
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data dan berkas berhasil diajukan!']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
            $logs = DB::table('employee_update_logs')->where('update_id', $id)->get();
            $dataToUpdate = [];

            foreach ($logs as $log) {
                $dataToUpdate[$log->field_name] = $log->new_value;
            }

            Employee::where('employee_no', $header->employee_no)->update($dataToUpdate);

            DB::table('employee_update_requests')->where('id', $id)->update([
                'approval_status' => 'Approved',
                'approved_by' => Auth::user()->employee_no,
                'approved_at' => now()
            ]);

            DB::table('employee_update_logs')->where('update_id', $id)->update(['approval_status' => 'Approved']);

            DB::commit();
            return back()->with('success', 'Data diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function getData($employee_no)
    {
        $employee = Employee::where('employee_no', $employee_no)->first();
        return $employee 
            ? response()->json(['success' => true, 'userData' => $employee->toArray()])
            : response()->json(['success' => false], 404);
    }
}