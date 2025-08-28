<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    public $table = 'assignments';
    protected $fillable = [
        'title',
        'description',
        'filePath',
        'dueDate',
        'maxScore',
        'courseId',
        'createdBy'
    ];

    protected $casts = [
        'dueDate' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Courses::class, 'courseId', 'courseId');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignmentId', 'id');
    }

    public function getStatusForStudent($studentId): string
    {
        $submission = $this->submissions()->where('studentId', $studentId)->first();
        
        if (!$submission) {
            return 'not_started';
        }
        
        if ($submission->score !== null) {
            return 'graded';
        }
        
        if ($submission->filePath) {
            return 'submitted';
        }
        
        return 'downloaded';
    }
}