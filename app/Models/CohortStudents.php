<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohortStudents extends Model
{
    use HasFactory;

    public $table = 'cohort_students';
    protected $fillable = [
        'cohortStudentId',
        'studentId',
        'cohortId',
        'courseId',
        'userId',
        'status',
    ];
    protected $primaryKey = 'cohortStudentId';

    public function cohorts()
    {
        return $this->belongsTo(Cohorts::class, 'cohortId', 'cohortId');
    } 

    public function courses()
    {
        return $this->belongsTo(Courses::class, 'courseId', 'courseId');
    } 

  
    
    public function payments()
    {
        return $this->hasOne(Payments::class, 'studentId', 'studentId');
    }
    
    public function completedModules()
    {
        return $this->hasMany(StudentModules::class, 'courseId', 'courseId')->where('status', 'completed');
            // ->withPivot('moduleId', 'status')
            // ->withTimestamps();
    }
    

    public function course_modules()
    {
        return $this->hasMany(CourseModules::class, 'courseId', 'courseId');
    }

    public function cohortCourses()
    {
        return $this->belongsTo(CohortCourses::class, 'cohortId', 'cohortId');
    }


}
