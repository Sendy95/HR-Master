<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLog extends Model
{
    protected $table = 'employee_logs';

    protected $fillable = [
        'employee_no',
        'activity',
        'details',
        'ip_address',
        'created_at'
    ];

    public $timestamps = false; // Karena kita manual insert 'created_at' di Controller

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_no', 'employee_no');
    }
}