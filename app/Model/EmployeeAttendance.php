<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    protected $table = 'employee_attendance';
    protected $primaryKey = 'employee_attendance_id';

    protected $fillable = [
        'employee_attendance_id',
        'finger_print_id',
        'in_out_time',
        'device',
        'device_employee_id',
        'employee_id',
        'type',
        'live_status',
        'status'
    ];

    public function deviceinfo()
    {
        return $this->belongsTo(Device::class, 'id');
    }

    public function employeeinfo()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
