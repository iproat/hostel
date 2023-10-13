<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LeaveConfigure extends Model
{
    protected $table = 'leave_configuration';
    protected $primaryKey = 'leave_config_id';

    protected $fillable = [
        'leave_config_id', 'designation','cl_days','sl_days','el_days'
    ];

    public function designationinfo(){
        return $this->belongsTo(Designation::class,'designation');
    }

}
