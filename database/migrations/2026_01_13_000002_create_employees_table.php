<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // --- KUNCI RELASI ---
            // Menggunakan onDelete('cascade') sesuai permintaan awal Anda
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // --- IDENTITAS UTAMA & PERUSAHAAN ---
            $table->timestamp('entry_timestamp')->useCurrent();
            $table->string('company_name', 50)->nullable(); 
            $table->enum('status', ['Lokal', 'Expat']);
            $table->string('employee_no', 10)->unique(); // No Karyawan
            $table->string('employee_name', 100);
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            
            // --- STRUKTUR ORGANISASI & KLASIFIKASI ---
            $table->string('department', 50)->nullable();
            $table->string('section', 50)->nullable();
            $table->string('sub_section', 50)->nullable();
            $table->string('position', 50)->nullable(); // Jabatan
            $table->string('salary_type', 50)->nullable();     // OT, All In
            $table->string('work_location', 50)->nullable();  // Office, Site
            $table->string('site_location', 50)->nullable();  // Lokasi Spesifik Site
            $table->string('employee_category', 50)->nullable(); // Operator, Staff
            $table->string('cost_center', 50)->nullable();
            
            // --- HIERARKI APPROVAL & GRUP KERJA ---
            $table->string('department_head_id', 50)->nullable();  // Untuk Approval Manager
            $table->string('direct_supervisor_id', 50)->nullable(); // Untuk Approval Atasan Langsung
            $table->string('auth_group', 50)->nullable(); // Otoritas Admin Cuti/OT
            $table->string('work_shift', 50)->nullable();  // Jam Kerja/Shift OT

            // --- BIODATA & KELUARGA ---
            $table->string('pob', 100)->nullable(); // Place of Birth
            $table->date('dob');               // Date of Birth (Password Awal)
            $table->enum('blood_type', ['A', 'A+', 'A-', 'B', 'B+', 'B-', 'AB', 'AB+', 'AB-', 'O', 'O+', 'O-'])->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('personal_email', 100)->unique();
            $table->string('company_email', 100)->unique()->nullable();
            $table->string('tribe', 50)->nullable();  // Suku
            $table->string('father_name', 50)->nullable(); 
            $table->string('mother_name', 50)->nullable(); 
            $table->string('nationality', 50)->default('Indonesia')->nullable(); 
            
            // --- KONTAK ---
            $table->string('phone_1', 50)->nullable();
            $table->string('phone_1_status', 50)->nullable();
            $table->string('phone_2', 50)->nullable(); 
            $table->string('phone_2_status', 50)->nullable(); 
            
            // --- DATA PEKERJAAN & PAYROLL ---
            $table->date('doj')->nullable(); // Date of Join
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('fixed_allowance', 15, 2)->default(0);
            $table->string('employment_status', 50)->nullable(); // Tetap, Kontrak, Probasi
            $table->string('bank_account_number', 50)->nullable();
            $table->text('bank_book_url')->nullable();
            $table->string('npwp_number', 50)->nullable();
            $table->text('npwp_url')->nullable();

            // --- PENDIDIKAN & DOKUMEN ---
            $table->string('education_level', 50)->nullable();
            $table->text('education_certificate_url')->nullable();
            $table->text('folder_link')->nullable();

            // --- IDENTITAS (KTP / PASSPORT) ---
            $table->string('identity_number', 50)->nullable();
            $table->string('identity_expiry', 50)->nullable();
            $table->text('identity_url')->nullable();
            $table->string('family_card_number', 50)->nullable(); 
            $table->text('family_card_url')->nullable();

            // --- DOKUMEN EXPAT (KITAS & IMTA) ---
            $table->string('kitas_number', 50)->nullable();
            $table->date('kitas_issued_date')->nullable();
            $table->date('kitas_end_date')->nullable();
            $table->string('imta_number', 50)->nullable();
            $table->date('imta_issued_date')->nullable();
            $table->date('imta_end_date')->nullable();

            // --- BPJS ---
            $table->string('bpjs_ketenagakerjaan', 50)->nullable();
            $table->string('bpjs_kesehatan', 50)->nullable();
            $table->string('bpjs_clinic', 100)->nullable();

            // --- STATUS PAJAK & PERNIKAHAN ---
            $table->string('marital_status', 50)->nullable();
            $table->string('family_status', 50)->nullable();
            $table->string('spouse_relation', 50)->nullable(); // Data riil internal
            $table->string('ptkp_status', 50)->nullable();   // Data hitungan pajak/payroll
            $table->string('spouse_name', 100)->nullable();
            $table->date('spouse_dob')->nullable();
            $table->integer('child_count')->nullable()->default(0);            
            $table->string('child_1_name', 100)->nullable();
            $table->string('child_1_relation', 50)->nullable();
            $table->date('child_1_dob')->nullable();
            $table->string('child_2_name', 100)->nullable();
            $table->string('child_2_relation', 50)->nullable();
            $table->date('child_2_dob')->nullable();
            $table->string('child_3_name', 100)->nullable();
            $table->string('child_3_relation', 50)->nullable();
            $table->date('child_3_dob')->nullable();

            // --- ALAMAT KTP ---
            $table->text('ktp_address')->nullable();
            $table->string('ktp_rt', 50)->nullable(); 
            $table->string('ktp_rw', 50)->nullable(); 
            $table->string('ktp_postal_code', 50)->nullable(); 
            $table->string('ktp_village', 50)->nullable(); 
            $table->string('ktp_district', 50)->nullable(); 
            $table->string('ktp_city', 50)->nullable(); 
            $table->string('ktp_province', 50)->nullable(); 

            // --- ALAMAT DOMISILI ---
            $table->text('current_address')->nullable();
            $table->string('current_rt', 50)->nullable(); 
            $table->string('current_rw', 50)->nullable(); 
            $table->string('current_postal_code', 50)->nullable(); 
            $table->string('current_village', 50)->nullable(); 
            $table->string('current_district', 50)->nullable(); 
            $table->string('current_city', 50)->nullable(); 
            $table->string('current_province', 50)->nullable(); 

            // --- KONTAK DARURAT 1 ---
            $table->string('emergency_name_1', 50)->nullable();
            $table->string('emergency_relation_1', 50)->nullable();
            $table->string('emergency_phone_1', 50)->nullable();
            $table->text('emergency_address_1')->nullable();
            $table->string('emergency_rt_1', 50)->nullable(); 
            $table->string('emergency_rw_1', 50)->nullable(); 
            $table->string('emergency_village_1', 50)->nullable(); 
            $table->string('emergency_district_1', 50)->nullable(); 
            $table->string('emergency_city_1', 50)->nullable(); 
            $table->string('emergency_province_1', 50)->nullable(); 

            // --- KONTAK DARURAT 2 ---
            $table->string('emergency_name_2', 50)->nullable();
            $table->string('emergency_relation_2', 50)->nullable();
            $table->string('emergency_phone_2', 50)->nullable();
            $table->text('emergency_address_2')->nullable();
            $table->string('emergency_rt_2', 50)->nullable();
            $table->string('emergency_rw_2', 50)->nullable();
            $table->string('emergency_village_2', 50)->nullable();
            $table->string('emergency_district_2', 50)->nullable();
            $table->string('emergency_city_2', 50)->nullable();
            $table->string('emergency_province_2', 50)->nullable();

            // --- STATUS & PEMUTUSAN KERJA ---
            $table->date('separation_date')->nullable();
            $table->string('separation_reason', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};