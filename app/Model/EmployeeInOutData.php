<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeInOutData extends Model
{

    protected $table = 'view_employee_in_out_data';
    protected $primaryKey = 'employee_attendance_id';
    protected $fillable = [
        "employee_attendance_id", "finger_print_id", "date", "in_time_from", "mrng_in_time", "mrng_out_time", "out_time_upto", "mrng_working_time", "working_hour", "in_out_time", "status", "created_at", "updated_at",
        "over_time", "early_by", "late_by", "shift_name", "device_name", "live_status", 'created_by', 'attendance_status',
        'updated_by', 'over_time_status', 'mandays', 'work_shift_id', 'eve_in_time', 'eve_out_time', 'eve_working_time', 'first_device', 'second_device',
        'm_in_time',
        'm_out_time',
        'af_in_time',
        'af_out_time',
        'e1_in_time',
        'e1_out_time',
        'e2_in_time',
        'e2_out_time',
        'n_in_time',
        'n_out_time',
        'm_status',
        'af_status',
        'e1_status',
        'e2_status',
        'n_status',
    ];
    protected $with = [
        'employee:finger_id,first_name,last_name,branch_id',
        'updatedBy:updated_by,employee_id,first_name,last_name',
    ];

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id', 'work_shift_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'finger_print_id', 'finger_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    public function scopeFilter($query, $status)
    {
        $query->whereHas('employee', function ($q) use ($status) {
            return $q->where('status', $status);
        });

        $query->select(
            "finger_print_id",
            "date",
            "in_time",
            "out_time",
            "working_time",
            "in_out_time",
            "status",
            "created_at",
            "updated_at",
            "over_time",
            "early_by",
            "late_by",
            "shift_name",
            'created_by',
            'updated_by',
            'over_time_status',
            'attendance_status',
            'work_shift_id',
            'mandays'
        );

        return $query;
    }

    public function scopeBranch($query, $branch)
    {
        return $query->whereHas('employee.branch', function ($q) use ($branch) {
            return $q->where('employee.branch_id', $branch);
        });
    }
}
