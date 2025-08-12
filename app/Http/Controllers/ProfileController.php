<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $user_session = Auth::user();
        $user = User::where('id', $user_session->id)->with('student_data')->first();
        return response()->json([
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'otherNames' => $user->otherNames,
            'email' => $user->email,
            'phoneNumber' => $user->phoneNumber,
            'alternatePhoneNumber' => $user->student_data->alternatePhoneNumber,
            'gender' => $user->student_data->gender,
            'maritalStatus' => $user->student_data->maritalStatus,
            
            'profilePicture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
            'dateOfBirth' => $user->date_of_birth,
            'stateOfResidence' => $user->student_data->stateOfResidence,
            'createdAt' => $user->created_at,
            'updatedAt' => $user->updated_at,
        ]);
    }

    /**
     * Update the authenticated user's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postalCode' => 'nullable|string|max:20',
            'dateOfBirth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle profile picture upload
        $profilePicturePath = $user->profile_picture;
        if ($request->hasFile('profilePicture')) {
            // Delete old profile picture if exists
            if ($profilePicturePath && Storage::exists($profilePicturePath)) {
                Storage::delete($profilePicturePath);
            }
            
            // Store new profile picture
            $file = $request->file('profilePicture');
            $fileName = 'profile_pictures/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $profilePicturePath = $file->storeAs('public', $fileName);
            $profilePicturePath = str_replace('public/', '', $profilePicturePath);
        }

        // Update user data
        $user->update([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'phone_number' => $request->phoneNumber,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postalCode,
            'date_of_birth' => $request->dateOfBirth,
            'gender' => $request->gender,
            'profile_picture' => $profilePicturePath,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number,
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'country' => $user->country,
                'postalCode' => $user->postal_code,
                'profilePicture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                'dateOfBirth' => $user->date_of_birth,
                'gender' => $user->gender,
                'createdAt' => $user->created_at,
                'updatedAt' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Delete the authenticated user's profile picture
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfilePicture()
    {
        $user = Auth::user();
        
        if ($user->profile_picture && Storage::exists($user->profile_picture)) {
            Storage::delete($user->profile_picture);
            $user->profile_picture = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile picture deleted successfully',
            'profilePicture' => null
        ]);
    }
}