<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModules extends Model
{
    use HasFactory;

    public $table = 'course_modules';
    protected $fillable = [
        'moduleId',
        'courseId',
        'title',
        'filePath',
        'status',
    ];
    protected $primaryKey = 'moduleId';

    protected $hidden = ['created_at', 'updated_at'];
}
