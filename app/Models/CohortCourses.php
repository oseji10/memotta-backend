<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohortCourses extends Model
{
    use HasFactory;

    public $table = 'cohort_courses';
    protected $fillable = [
        'cohortCourseId',
        'courseId',
        'cohortId',
    ];
    protected $primaryKey = 'cohortCourseId';

    public function cohorts()
    {
        return $this->belongsTo(Cohorts::class, 'cohortId', 'cohortId');
    } 

    public function courses()
    {
        return $this->belongsTo(Courses::class, 'courseId', 'courseId');
    } 
}
