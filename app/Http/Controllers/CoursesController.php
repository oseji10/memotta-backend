<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\CourseModules;
use App\Models\Resources;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CoursesController extends Controller
{
    public function index()
    {
        // This method should return a list of courses
        $courses = Courses::with('instructor')->get();
        if (!$courses) {
            return response()->json(['message' => 'No courses found'], 404);
        }
        return response()->json($courses);
    }

    public function availableCourses()
    {
        // This method should return a list of courses
        $courses = Courses::with('cohorts')->where('status', 'active')->get();
        if ($courses->isEmpty()) {
            return response()->json(['message' => 'No courses found'], 404);
        }
        return response()->json($courses);
    }


     public function courseModules(Request $request, $id)
    {
        // This method should return a list of course modules
        $course = CourseModules::where('courseId', $id)->get();
        if (!$course) {
            return response()->json(['message' => 'No modules added'], 404);
        }
        return response()->json($course);
    }

     public function addModules(Request $request)
    {
       $validated = $request->validate([
        'title' => 'nullable|string|max:255',
        'courseId' => 'nullable|max:255',
        ]);

       
        $course = CourseModules::create($validated);
        return response()->json($course, 201); // HTTP status code 201: Created

    }


       public function addResources(Request $request)
    {
       $validated = $request->validate([
        // 'file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        'courseId' => 'nullable|max:255',
        ]);
        $course = Courses::where('courseId', $request->courseId)->first();
        if ($request->hasFile('file')) {
            $validated['title'] = $course->courseName;
            $validated['filePath'] = $request->file('file')->store('file', 'public');
        }

       
        $course = Resources::create($validated);
        return response()->json($course, 201); // HTTP status code 201: Created

    }
   

     public function getResources(Request $request, $id)
    {
        // This method should return a list of course modules
        $course = Resources::where('courseId', $id)->get();
        if (!$course) {
            return response()->json(['message' => 'No resources found'], 404);
        }
        return response()->json($course);
    }
       
     public function store(Request $request)
    {
       $validated = $request->validate([
        'courseName' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'cost' => 'nullable|string|max:255',
        'duration' => 'nullable|string|max:255',
        'instructor' => 'nullable|max:255',
        ]);

        $validated['addedBy'] = Auth::id(); // Assuming the user is authenticated and has an ID
        $course = Courses::create($validated);
        return response()->json($course, 201); // HTTP status code 201: Created

    }

   public function update(Request $request, $id)
{
    $validated = $request->validate([
        'courseName' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:255',
        'cost' => 'nullable|string|max:255',
        'duration' => 'nullable|string|max:255',
        'instructor' => 'nullable|max:255',
    ]);

    $course = Courses::where('courseId', $id)->first();
    if (!$course) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    $course->update($validated);
    
    return response()->json([
        'courseId' => $course->cancerId,
        'courseName' => $course->courseName,
        'description' => $course->description,
        'duration' => $course->duration,
        'cost' => $course->cost,
        'instructor' => $course->instructor
    ], 200);
}


   public function changeStatus(Request $request, $id)
{
   $course = Courses::where('courseId', $id)->first();
    if (!$course) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    $course->update(['status' => $request->status]);
    
    return response()->json([
        'message' => 'Course status changed successfully'
    ], 200);
}

    public function destroy($id)
    {
        $course = Courses::where('courseId', $id)->first();
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->delete();
        return response()->json(['message' => 'Course deleted successfully'], 200);
    }

  
   
}
