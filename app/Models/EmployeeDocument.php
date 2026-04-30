<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'document_type',
        'file_path',
        'upload_count',
    ];

    /**
     * Relasi balik ke Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * Aksesor untuk mendapatkan URL lengkap yang bisa diklik
     */
    public function getUrlAttribute()
    {
        return asset($this->file_path);
    }
}