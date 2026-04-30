<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_update_requests', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no');
            $table->string('approval_status')->default('Pending'); // Pending, Approved, Rejected
            $table->string('requested_by'); // NIK karyawan yang mengajukan
            $table->string('request_ip')->nullable();
            $table->string('request_source')->default('Employee'); // e.g., Employee, Admin
            $table->string('approved_by')->nullable(); // NIK Admin yang menyetujui
            $table->timestamp('approved_at')->nullable();
            $table->text('reject_reason')->nullable(); // Opsional: alasan jika ditolak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_update_requests');
    }
};