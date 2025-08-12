<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentModules extends Model
{
    use HasFactory;

    public $table = 'student_modules';
    protected $fillable = [
        'studentModuleId',
        'courseId',
        'studentId',
        'moduleId',
        'status',
    ];
    protected $primaryKey = 'studentModuleId';

    protected $hidden = ['created_at', 'updated_at'];
}
