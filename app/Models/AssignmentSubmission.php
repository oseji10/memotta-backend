<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    public $table = 'assignment_submissions';
    protected $fillable = [
        'assignmentId',
        'studentId',
        'filePath',
        'submittedAt',
        'score',
        'feedback',
        'gradedAt'
    ];

    protected $casts = [
        'submittedAt' => 'datetime',
        'gradedAt' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function cohort_students()
    {
        return $this->belongsTo(User::class, 'id', 'studentId');
    }
}