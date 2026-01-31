<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmployeeInitialSeeder extends Seeder
{
    public function run(): void
    {
        // Data karyawan dari log HeidiSQL Anda
        $employees = [
            ['no' => '15021649', 'name' => 'Sendy', 'gender' => 'Laki-laki', 'pob' => 'TANJUNGPINANG', 'dob' => '16-Sep-1995', 'blood' => 'O', 'rel' => 'Buddha', 'email' => 'cruz.sendy95@gmail.com', 'phone' => '085765369012'],
            ['no' => '01589', 'name' => 'Supriyono', 'gender' => 'Laki-laki', 'pob' => 'Tanjungpinang', 'dob' => '25-Feb-1971', 'blood' => null, 'rel' => 'Islam', 'email' => 'Suprinaura71@Gmail.Com', 'phone' => '082173750502'],
            ['no' => '22061960', 'name' => 'Ari Fernando Rachmat', 'gender' => 'Laki-laki', 'pob' => 'Tanjungpinang', 'dob' => '24-Oct-1992', 'blood' => 'O', 'rel' => 'Buddha', 'email' => 'Arifernandoshmkn@Gmail.Com', 'phone' => '081363738350'],
            ['no' => '23112144', 'name' => 'Ita Esriani Br Sembiring', 'gender' => 'Perempuan', 'pob' => 'Kedeberek', 'dob' => '29-Jul-2000', 'blood' => 'O', 'rel' => 'Kristen', 'email' => 'Itaesriani12@Gmail.Com', 'phone' => '085263462200'],
            ['no' => '24062253', 'name' => 'Anggita Aldanawary D.K', 'gender' => 'Perempuan', 'pob' => 'Batam', 'dob' => '07-Jan-1995', 'blood' => 'B', 'rel' => 'Islam', 'email' => 'Anggitaaldanawarydk@Gmail.Com', 'phone' => '081290815520'],
            ['no' => '25082488', 'name' => 'Dwi Yolanda Puspita Sari', 'gender' => 'Perempuan', 'pob' => 'Banjarmasin', 'dob' => '10-Jun-1992', 'blood' => 'O', 'rel' => 'Islam', 'email' => 'Dwiyolanda1993@Gmail.Com', 'phone' => '085765124688'],
            ['no' => '25082491', 'name' => 'Helina', 'gender' => 'Perempuan', 'pob' => 'Sugi', 'dob' => '15-Nov-1974', 'blood' => 'O', 'rel' => 'Kristen', 'email' => 'Helinatham@Gmail.Com', 'phone' => '081276912847'],
            ['no' => '25092517', 'name' => 'Ravika Pakpahan', 'gender' => 'Perempuan', 'pob' => 'Sibingke', 'dob' => '29-Nov-1998', 'blood' => 'AB', 'rel' => 'Kristen', 'email' => 'Ravikapakpahan2911@Gmail.Com', 'phone' => '082283730455'],
            ['no' => '25092518', 'name' => 'Derry Mardiansyah', 'gender' => 'Laki-laki', 'pob' => 'Batam', 'dob' => '02-May-2001', 'blood' => 'AB', 'rel' => 'Islam', 'email' => 'Derryworld1@Gmail.Com', 'phone' => '082179972126'],
            ['no' => '25122599', 'name' => 'Bunga Putri Zhakina', 'gender' => 'Perempuan', 'pob' => 'Batam', 'dob' => '24-Nov-1998', 'blood' => 'AB', 'rel' => 'Islam', 'email' => 'Zhakina2411@Gmail.Com', 'phone' => '081312920837'],
            ['no' => '25122606', 'name' => 'Nasir Pauzi', 'gender' => 'Laki-laki', 'pob' => 'Sukabumi', 'dob' => '17-Feb-2003', 'blood' => 'A', 'rel' => 'Islam', 'email' => 'Nasirpauzi@Gmail.Com', 'phone' => '085794248367'],
            ['no' => '71016', 'name' => 'Herawati', 'gender' => 'Perempuan', 'pob' => 'Kampung Melati', 'dob' => '20-Jun-1963', 'blood' => null, 'rel' => 'Islam', 'email' => 'Herawatihasan1963@Gmail.Com', 'phone' => '08127012052'],
        ];

        foreach ($employees as $data) {
            // Parsing tanggal lahir untuk format password (ddmmyyyy) dan format DB (Y-m-d)
            $birthDate = Carbon::parse($data['dob']);

            // 1. Buat User Login
            $user = User::updateOrCreate(
                ['employee_no' => $data['no']], // Unik berdasarkan nomor karyawan
                [
                    'password' => Hash::make($birthDate->format('dmY')), // Contoh: 16091995
                    'role' => 'employee',
                    'is_active' => true,
                ]
            );

            // 2. Buat Data Employee (PDM)
            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_no' => $data['no'],
                    'employee_name' => $data['name'],
                    'gender' => $data['gender'],
                    'pob' => $data['pob'],
                    'dob' => $birthDate->format('Y-m-d'),
                    'blood_type' => $data['blood'],
                    'religion' => $data['rel'],
                    'personal_email' => $data['email'],
                    'phone_1' => $data['phone'],
                    'status' => 'Lokal',
                    'company_name' => 'PT. Contoh Perusahaan', // Sesuaikan nama perusahaan
                ]
            );
        }
    }
}