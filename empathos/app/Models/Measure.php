<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'experienceResultID',
        'bpm',
        'ibi',
        'sdnn',
        'sdsd',
        'rmssd',
        'pnn20',
        'pnn50'
    ];
}
