<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceResult extends Model
{
    use HasFactory;

    protected $casts = ['results' => 'array'];

    protected $fillable = [
        'id',
        'userID',
        'experienceID',
        'startTime',
        'userEmotion',
        'valence',
        'arousal',
        'dominance',
        'results',
        'frequency'
    ];
}
