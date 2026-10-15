<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = "lessons";

    protected $fillable = [
        'title',
        'sign_reference',
        'landmarks_data',
        'similarity_threshold',
        'difficulty'
    ];
}
