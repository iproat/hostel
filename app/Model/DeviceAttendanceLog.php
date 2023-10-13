<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeviceAttendanceLog extends Model
{
    protected $table='ms_sql';
    protected $primaryKey='primary_id';

    protected $fillable = [
        'primary_id', 'ID', 'type', 'datetime', 'employee','device'
    ];


    public function deviceinfo(){
        return $this->belongsTo(Device::class,'id');
    }

    public function employeeinfo(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }




}
