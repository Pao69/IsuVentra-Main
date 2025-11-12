<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    protected $fillable = [
        'student_id',
        'event_id',
        'time_in',
        'time_out'
    ];
}
