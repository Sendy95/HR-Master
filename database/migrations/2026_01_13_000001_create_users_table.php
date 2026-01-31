<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no')->unique();
            $table->string('email')->unique();
            $table->string('password');
            
            // Role dengan standar underscore
            $table->enum('role', [
                'super_admin', 
                'hr_manager',
                'hr_staff',
                'assistant_hr_manager',
                'hr_generalist',
                'hr_payroll',
                'hr_admin',
                'admin',
                'it',
                'hod', 
                'manager', 
                'assistant_manager', 
                'employee'
            ])->default('employee');

            // Fitur Keamanan & Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('password_changed_at')->nullable(); // Jika NULL, wajib ganti password
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            
            // Audit Log Login
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};