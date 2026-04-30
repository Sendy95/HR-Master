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
        'name',                // Tambahkan ini agar tidak blank saat login
        'email',               // Tambahkan ini agar tidak blank saat login
        'password',
        'password_changed_at', // Tambahkan ini untuk fitur paksa ganti password
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
        'password_changed_at' => 'datetime', // Tambahkan cast agar mudah dimanipulasi Carbon
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke profil Employee
     */
    public function employee()
    {
        // Menyambungkan tabel users ke tabel employees melalui employee_no
        return $this->hasOne(Employee::class, 'employee_no', 'employee_no');
    }
}