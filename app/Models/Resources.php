<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    protected $fillable = [
        'resourceId',
        'courseId',
        'title',
        'type', // pdf, video, link
        'filePath',
        'externalUrl'
    ];
    
    protected $primaryKey = 'resourceId';

    public function course()
    {
        return $this->belongsTo(Courses::class);
    }
}