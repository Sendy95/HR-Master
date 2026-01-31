<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_updates', function (Blueprint $table) {
            $table->id();
            // Relasi ke karyawan yang mengajukan
            $table->foreignId('employee_no')->constrained('employees')->onDelete('cascade');

            // Informasi kategori update (Pribadi, Alamat, Perbankan, dll)
            $table->string('category')->nullable();

            // Menggunakan JSON agar fleksibel: karyawan bisa ubah 1 field atau 10 field sekaligus
            $table->json('requested_changes'); 

            // Data lama saat pengajuan dibuat (Agar HR bisa bandingkan dengan data asli)
            $table->json('original_data')->nullable(); 

            $table->enum('status', ['pending', 'approved', 'partial', 'rejected'])->default('pending');

            // Catatan dari HR (misal: alasan kenapa ditolak)
            $table->text('hr_note')->nullable();
            
            // Siapa HR yang menyetujui/menolak
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_updates');
    }
};