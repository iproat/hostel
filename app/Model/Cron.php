<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    protected $table = 'cron';
    protected $primaryKey = 'cron_id';

    protected $fillable = [
        'cron_id', 'type', 'status'
    ];
}
