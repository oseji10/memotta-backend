<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
  public function index()
    {
        $user = Auth::user();
        
        // Eager load the role relationship to avoid N+1 queries
        $user->load('role_details');
        
        // For students, get assignments for their courses
        if ($user->isStudent()) {
            $assignments = Assignment::whereHas('course', function($query) use ($user) {
                $query->whereHas('cohort_students', function($q) use ($user) {
                    $q->where('userId', $user->id);
                });
            })->with(['submissions' => function($query) use ($user) {
                $query->where('studentId', $user->id);
            }])->get();
            
            // Add status and score to each assignment
            $assignments->each(function($assignment) use ($user) {
                $submission = $assignment->submissions->first();
                $assignment->status = $assignment->getStatusForStudent($user->id);
                $assignment->score = $submission->score ?? null;
                $assignment->feedback = $submission->feedback ?? null;
                $assignment->submission_url = $submission->filePath ?? null;
                unset($assignment->submissions);
            });
            
            return response()->json($assignments);
        }
        
        // For instructors, get all assignments they created
        if ($user->isInstructor()) {
            return response()->json(Assignment::where('createdBy', $user->id)->get());
        }
        
        // For admins, get all assignments
        if ($user->isAdmin()) {
            return response()->json(Assignment::all());
        }
        
        return response()->json([]);
    }

    // public function download(Assignment $assignment)
    // {
    //     // Check if user has access to this assignment
    //     if (!auth()->user()->can('download', $assignment)) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }
        
    //     // Record download for student
    //     if (auth()->user()->hasRole('STUDENT')) {
    //         $submission = AssignmentSubmission::firstOrCreate([
    //             'assignmentId' => $assignment->id,
    //             'studentId' => auth()->id(),
    //         ], [
    //             'submittedAt' => null,
    //         ]);
    //     }
        
    //     return Storage::download($assignment->filePath);
    // }

   public function download(Assignment $assignment)
{
    if (!auth()->user()->can('download', $assignment)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
         if (auth()->user()->hasRole('STUDENT')) {
            $submission = AssignmentSubmission::firstOrCreate([
                'assignmentId' => $assignment->id,
                'studentId' => auth()->id(),
            ], [
                'submittedAt' => null,
            ]);
        }

    return Storage::disk('public')->download($assignment->filePath, basename($assignment->filePath));
}


    public function submit(Request $request, Assignment $assignment)
    {
        // return $assignment;
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,zip|max:10240',
        ]);
        
        // Check if user is a student and has access to this assignment
        $user = auth()->user();
        // if (!$user->hasRole('STUDENT') || !$user->can('submit', $assignment)) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        if (!auth()->user()->can('submit', $assignment)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // Check if assignment is still open for submission
        if ($assignment->dueDate->isPast()) {
            return response()->json(['error' => 'Assignment due date has passed'], 400);
        }
        
        // Store the uploaded file
        $filePath = $request->file('file')->store('submissions');
        
        // Create or update submission
        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignmentId' => $request->assignmentId,
                'studentId' => $user->id,
            ],
            [
                'filePath' => $filePath,
                'submittedAt' => now(),
            ]
        );
        
        return response()->json([
            'message' => 'Assignment submitted successfully',
            'submission' => $submission,
        ]);
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:'.$submission->assignment->max_score,
            'feedback' => 'nullable|string',
        ]);
        
        // Check if user is instructor and owns the assignment
        $user = auth()->user();
        if (!$user->hasRole('INSTRUCTOR') || $submission->assignment->createdBy !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'gradedAt' => now(),
        ]);
        
        return response()->json([
            'message' => 'Assignment graded successfully',
            'submission' => $submission,
        ]);
    }
}