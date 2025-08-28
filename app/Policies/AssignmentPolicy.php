<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignmentPolicy
{
    public function download(User $user, Assignment $assignment)
    {
        // Load the role relationship
        $user->load('role_details');
        
        // Students can download if they're enrolled in the course
        if ($user->isStudent()) {
            return $assignment->course->cohort_students->exists($user->id)
                ? Response::allow()
                : Response::deny('You are not enrolled in this course');
        }
        
        // Instructors can download their own assignments
        if ($user->isInstructor()) {
            return $assignment->created_by === $user->id
                ? Response::allow()
                : Response::deny('This is not your assignment');
        }
        
        // Admins can download any assignment
        if ($user->isAdmin()) {
            return Response::allow();
        }
        
        return Response::deny('Unauthorized action');
    }

    public function submit(User $user, Assignment $assignment)
    {
        // Load the role relationship
        $user->load('role_details');
        
        // Only students can submit assignments
        if (!$user->isStudent()) {
            return Response::deny('Only students can submit assignments');
        }
        
        // Check if student is enrolled in the course
        // if (!$assignment->course->cohort_students->exists($user->id)) {
        //     return Response::deny('You are not enrolled in this course');
        // }

        if ($user->isStudent()) {
            return $assignment->course->cohort_students->exists($user->id)
                ? Response::allow()
                : Response::deny('You are not enrolled in this course');
        }
        
        // Check if assignment is still open
        if ($assignment->dueDate->isPast()) {
            return Response::deny('Assignment due date has passed');
        }
        
        return Response::allow();
    }
}