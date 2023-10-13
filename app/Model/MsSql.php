<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MsSql extends Model
{
    protected $table      = "ms_sql";
    protected $primaryKey = 'primary_id';

    protected $fillable = [
        'primary_id',
        'ID',
        'datetime',
        'status',
        'created_at',
        'updated_at',
        'device_empoloyee_id',
        'device',
        'employee'
    ];
    public function Employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'employee_id', 'employee');
    }
}
