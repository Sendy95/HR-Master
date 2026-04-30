<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan User ID 1 ada terlebih dahulu
        $user = User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Sendy',
                'email' => 'cruz.sendy95@gmail.com',
                'password' => Hash::make('16091995'), // Contoh password default ddmmyyyy
            ]
        );

        // 2. Input data detail Employee
        Employee::updateOrCreate(
            ['employee_no' => '15021649'],
            [
                'user_id'             => $user->id,
                'entry_timestamp'     => Carbon::parse('2026-01-16 12:12:00'),
                'company_name'        => 'ASL',
                'status'              => 'Local',
                'employee_name'       => 'Sendy',
                'gender'              => 'Male',
                'pob'                 => 'Tanjungpinang',
                'dob'                 => '1995-09-16',
                'blood_type'          => 'O',
                'religion'            => 'Buddhism',
                'personal_email'      => 'cruz.sendy95@gmail.com',
                'nationality'         => 'Indonesia',
                'phone_1'             => '085765369012',
                'phone_1_status'      => 'WhatsApp & Phone',
                'marital_status'      => 'Single',
                'basic_salary'        => 0,
                'fixed_allowance'     => 0,
                
                // Kolom lainnya diset null sesuai data \N Anda
                'department'          => null,
                'section'             => null,
                'sub_section'         => null,
                'position'            => null,
                'salary_type'         => null,
                'work_location'       => null,
                'site_location'       => null,
                'employee_category'   => null,
                'company_email'       => null,
                'employment_status'   => null,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ]
        );
    }
}