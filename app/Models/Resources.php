<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    protected $fillable = [
        'courseId',
        'title',
        'type', // pdf, video, link
        'filePath',
        'externalUrl'
    ];
    
    public function course()
    {
        return $this->belongsTo(Courses::class);
    }
}