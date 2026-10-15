<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';
    protected $fillable = [
        'user_id',
        'lesson_id',
        'quiz_score',
        'response_time_ms',
        'match_accuracy',
        'completed'
    ];
}
