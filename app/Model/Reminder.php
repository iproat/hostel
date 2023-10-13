<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = 'reminder';
    protected $primaryKey = 'reminder_id';

    protected $fillable = [
        'reminder_id', 'title','content','expiry_date','status','last_reminder','file'
    ];

   
}
