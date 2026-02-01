<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateRequest extends Model { 
    protected $table = 'employee_update_requests';
    protected $guarded = [];

    public function logs() {
        return $this->hasMany(EmployeeUpdateLog::class, 'update_id', 'id');
    }
}