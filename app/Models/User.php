<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_no',
        'password',
        'role',
        'is_active',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'otp_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke profil Employee
     * Masukkan fungsi ini di DALAM class
     */
    public function employee()
    {
        // Gunakan employee_no jika itu adalah kunci penyambungnya
        return $this->hasOne(Employee::class, 'employee_no', 'employee_no');
    }
} 