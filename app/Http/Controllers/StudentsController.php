<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\CohortStudents;
use App\Models\Payment;
use App\Models\Resources;
use Illuminate\Support\Facades\Auth;

class StudentsController extends Controller
{
    /**
     * Get all courses for the authenticated student with payment status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentCourses()
    {
        try {
            $userId = Auth::id();
            
            // Get all enrollments for the student with related course and payment info
            $enrollments = CohortStudents::with(['courses', 'payments'])
                ->where('userId', $userId)
                ->get();
            
            // Transform the data for the frontend
            $courses = $enrollments->map(function ($enrollment) {
                $paymentStatus = $enrollment->payments->paymentStatus;
                // && 
                $paymentConfirmed = $enrollment->payments->paymentStatus === 'PAID';

                return [
                    // 'id' => $enrollment->course->courseId,
                    'courseId' => $enrollment->courses->courseId,
                    'name' => $enrollment->courses->courseName,
                    'description' => $enrollment->courses->description,
                    'progress' => $this->calculateProgress($enrollment),
                    'resources' => $paymentConfirmed 
                        ? $this->getCourseResources($enrollment->courseId)
                        : [],
                    'cohort' => $enrollment->courses->cohort_courses->cohorts->cohortName ?? null,
                    'startDate' => $enrollment->courses->cohort_courses->startDate ?? null,
                    'endDate' => $enrollment->courses->cohort_courses->endDate ?? null,
                    'paymentStatus' => $paymentStatus,
                    'enrollmentStatus' => $enrollment->status ?? null,
                ];
            });
            
            return response()->json($courses);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch student courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate course progress percentage
     *
     * @param Enrollment $enrollment
     * @return int
     */
    protected function calculateProgress($enrollment)
    {
        // Implement your progress calculation logic here
        // This could be based on completed modules, assignments, etc.
        // For example:
        $totalModules = $enrollment->courses->course_modules()->count();
        $completedModules = $enrollment->completedModules()->count();
        
        return $totalModules > 0 
            ? (int) round(($completedModules / $totalModules) * 100)
            : 0;
    }
    
    /**
     * Get formatted resources for a course
     *
     * @param int $courseId
     * @return array
     */
    protected function getCourseResources($courseId)
    {
        return Resources::where('courseId', $courseId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => $resource->resourceId,
                    'title' => $resource->title,
                    'type' => $resource->type, // pdf, video, link
                    // 'filePath' => $resource->filePath,
                    'filePath' => $this->getResourceUrl($resource),
                    'externalUrl' => $resource->externalUrl,
                    'created_at' => $resource->created_at->toDateTimeString(),
                ];
            })
            ->toArray();
    }
    
    /**
     * Generate the appropriate URL for the resource
     *
     * @param Resource $resource
     * @return string
     */
    protected function getResourceUrl($resource)
    {
        switch ($resource->type) {
            case 'pdf':
                return route('resources.download', $resource->resourceId);
            case 'video':
                return route('resources.stream', $resource->resourceId);
            case 'link':
                return $resource->externalUrl;
            default:
                return '#';
        }
    }
}