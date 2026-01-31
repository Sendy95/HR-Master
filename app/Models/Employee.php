<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    protected $table = 'employees'; 

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'company_name',
        'status',
        'employee_no',
        'employee_name',
        'gender',
        
        // --- Struktur Organisasi & Klasifikasi ---
        'department',
        'section',
        'sub_section',
        'position',
        'salary_type',
        'work_location',
        'site_location',
        'employee_category',
        'cost_center',
        
        // --- Hierarchy & Work Group ---
        'department_head_id',
        'direct_supervisor_id',
        'auth_group',
        'work_shift',

        // --- Biodata ---
        'pob',
        'dob',
        'blood_type',
        'religion',
        'personal_email',
        'company_email',
        'tribe',
        'father_name',
        'mother_name',
        'nationality',
        
        // --- Kontak ---
        'phone_1',
        'phone_1_status',
        'phone_2',
        'phone_2_status',
        
        // --- Payroll & Data Pekerjaan ---
        'doj',
        'basic_salary',
        'fixed_allowance',
        'employment_status',
        'bank_account_number',
        'bank_book_url',
        'npwp_number',
        'npwp_url',

        // --- Pendidikan & Dokumen ---
        'education_level',
        'education_certificate_url',
        'folder_link',
        'identity_number',
        'identity_expiry', // Tetap string agar bisa "Seumur Hidup"
        'identity_url',
        'family_card_number',
        'family_card_url',

        // --- Dokumen Expat ---
        'kitas_number',
        'kitas_issued_date',
        'kitas_end_date',
        'imta_number',
        'imta_issued_date',
        'imta_end_date',

        // --- BPJS ---
        'bpjs_ketenagakerjaan',
        'bpjs_kesehatan',
        'bpjs_clinic',

        // --- Status Pajak & Keluarga ---
        'marital_status',
        'family_status',
        'ptkp_status',
        'spouse_name',
        'spouse_relation',
        'spouse_dob',
        'child_count',
        'child_1_name',
        'child_1_relation',
        'child_1_dob',
        'child_2_name',
        'child_2_relation',
        'child_2_dob',
        'child_3_name',
        'child_3_relation',
        'child_3_dob',

        // --- Alamat KTP ---
        'ktp_address', 'ktp_rt', 'ktp_rw', 'ktp_postal_code', 
        'ktp_village', 'ktp_district', 'ktp_city', 'ktp_province', 

        // --- Alamat Domisili ---
        'current_address', 'current_rt', 'current_rw', 'current_postal_code', 
        'current_village', 'current_district', 'current_city', 'current_province', 

        // --- Kontak Darurat 1 ---
        'emergency_name_1', 'emergency_relation_1', 'emergency_phone_1', 'emergency_address_1',
        'emergency_rt_1', 'emergency_rw_1', 'emergency_village_1', 'emergency_district_1', 
        'emergency_city_1', 'emergency_province_1',

        // --- Kontak Darurat 2 ---
        'emergency_name_2', 'emergency_relation_2', 'emergency_phone_2', 'emergency_address_2',
        'emergency_rt_2', 'emergency_rw_2', 'emergency_village_2', 'emergency_district_2', 
        'emergency_city_2', 'emergency_province_2',

        // --- Status Akhir ---
        'separation_date',
        'separation_reason',
    ];

    /**
     * Casts kolom ke tipe data tertentu
     */
    protected $casts = [
        'dob' => 'date',
        'doj' => 'date',
        // 'identity_expiry' dilepas dari date cast agar support string "Seumur Hidup"
        'spouse_dob' => 'date',
        'child_1_dob' => 'date',
        'child_2_dob' => 'date',
        'child_3_dob' => 'date',
        'kitas_issued_date' => 'date',
        'kitas_end_date' => 'date',
        'imta_issued_date' => 'date',
        'imta_end_date' => 'date',
        'separation_date' => 'date',
        'basic_salary' => 'decimal:2',
        'fixed_allowance' => 'decimal:2',
        'child_count' => 'integer',
    ];

    /**
     * RELASI KE USER
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Mutator DOB: Memastikan format tersimpan Y-m-d di Database
     */
    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    /**
     * Accessor DOB: Contoh jika ingin tampilan default selalu d-M-Y
     */
    public function getFormattedDobAttribute()
    {
        return $this->dob ? $this->dob->format('d-M-Y') : null;
    }
}