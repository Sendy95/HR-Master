<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeUpdate extends Model
{
    protected $table = 'employee_updates';

    // Kita masukkan semua kolom yang bisa di-update oleh karyawan
    protected $fillable = [
        'employee_no', 'employee_name', 'company_name', 'status', 'gender',
        'pob', 'dob', 'blood_type', 'religion', 'personal_email', 'tribe',
        'phone_1', 'phone_1_status', 'phone_2', 'phone_2_status',
        'education_level', 'bank_account_number', 'npwp_number',
        'identity_number', 'identity_expiry', 'family_card_number',
        'marital_status', 'family_status', 'ptkp_status',
        'spouse_name', 'spouse_relation', 'spouse_dob',
        'child_count', 'child_1_name', 'child_1_relation', 'child_1_dob',
        'child_2_name', 'child_2_relation', 'child_2_dob',
        'child_3_name', 'child_3_relation', 'child_3_dob',
        'identity_url', 'education_certificate_url', 'bank_book_url', 
        'npwp_url', 'family_card_url',
        'approval_status', 'hr_note'
    ];

    protected $casts = [
        'dob' => 'date',
        'spouse_dob' => 'date',
        'child_1_dob' => 'date',
        'child_2_dob' => 'date',
        'child_3_dob' => 'date',
        'updated_at' => 'datetime'
    ];
}