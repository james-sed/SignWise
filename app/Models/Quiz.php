<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz';
    protected $fillable = ['lesson_id', 'title', 'question_text', 'target_sign', 'passing_score'];
}
