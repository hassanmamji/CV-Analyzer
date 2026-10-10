<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvAnalysis extends Model
{
    protected $fillable = [
        'original_name', 'file_path', 'extracted_text',
        'job_role', 'score', 'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];
}