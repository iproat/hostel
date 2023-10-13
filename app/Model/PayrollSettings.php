<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PayrollSettings extends Model
{
    protected $table = 'payroll_settings';
    protected $primaryKey = 'payset_id';

    protected $fillable = ['basic','hra','employee_esic','employee_pf','working_days'];



}
