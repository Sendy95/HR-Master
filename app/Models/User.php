<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_no',      // Username login
        'password',         // Format ddmmyyyy (saat seeder)
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

    // Relasi ke profil Employee untuk mengambil Nama, dll
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }
}