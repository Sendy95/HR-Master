<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['id' => 1], // Unik berdasarkan ID
            [
                'employee_no'         => '15021649',
                'name'                => 'Sendy',
                'email'               => 'cruz.sendy95@gmail.com',
                // Password default dari data Anda (biasanya hash dari 'password')
                'password'            => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role'                => 'super_admin',
                'is_active'           => 1,
                'password_changed_at' => null,
                'otp_code'            => null,
                'otp_expires_at'      => null,
                'last_login_at'       => null,
                'last_login_ip'       => null,
                'remember_token'      => null,
                'created_at'          => Carbon::parse('2026-01-16 12:11:48'),
                'updated_at'          => Carbon::parse('2026-01-16 12:11:48'),
            ]
        );
    }
}