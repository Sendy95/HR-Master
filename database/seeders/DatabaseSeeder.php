<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Gunakan WithoutModelEvents jika Anda ingin proses seeding lebih cepat tanpa memicu Event Model
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil seeder spesifik secara berurutan
        $this->call([
            UserSeeder::class,
            EmployeeSeeder::class,
        ]);
        
        // Catatan: UserSeeder harus di atas EmployeeSeeder 
        // agar ID user tersedia saat data employee diinput.
    }
}