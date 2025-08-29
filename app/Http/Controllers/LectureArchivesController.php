<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LectureArchives;
class LectureArchivesController extends Controller
{
    public function index()
    {
        $lectures = LectureArchives::all();
        return response()->json($lectures);
       
    }

  

    public function store(Request $request)
    {
        // Directly get the data from the request
        $data = $request->all();
    
        // Create a new user with the data (ensure that the fields are mass assignable in the model)
        $lectures = LectureArchives::create($data);

        // Return a response, typically JSON
        return response()->json($lectures, 201); // HTTP status code 201: Created
    }
    
}
