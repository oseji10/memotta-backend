<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    public $table = 'courses';
    protected $primaryKey = 'courseId';
    protected $fillable = [
        'courseName',
        'duration',
        'description',
        'image',
        'cost',
        'status',
    ];


     public function cohort_courses()
    {
        return $this->hasOne(CohortCourses::class, 'courseId', 'courseId');
    }

     public function resources()
    {
        return $this->hasMany(Resources::class, 'courseId', 'courseId');
    }

    public function course_modules()
    {
        return $this->hasMany(CourseModules::class, 'courseId', 'courseId');
    }
    
}
