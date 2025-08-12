<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cohorts extends Model
{
    use HasFactory;

    public $table = 'cohorts';
    protected $fillable = [
        'cohortId',
        'cohortName',
        'status',
    ];
    protected $primaryKey = 'cohortId';

    public function cohortCourses()
    {
        return $this->hasMany(CohortCourses::class, 'cohortId', 'cohortId');
    }

   
}
