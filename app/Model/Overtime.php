<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $table = 'employee_overtime';
    protected $primaryKey = 'employee_over_time_id';

    protected $fillable = [
        'employee_over_time_id', 'date','amount_of_deduction','status','created_by','updated_by'
    ];
}
