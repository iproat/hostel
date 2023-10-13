<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GeneralSettings extends Model
{
    protected $table = 'general_settings';
    protected $primaryKey = 'genset_id';

    protected $fillable = [
        'genset_id', 'email_ids'
    ];


}
