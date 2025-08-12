<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        // Verify token with Google
        $googleUser = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->token
        ])->json();

        if (isset($googleUser['error_description'])) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }

        // Check if user exists
        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser['name'] ?? $googleUser['email'],
                'email' => $googleUser['email'],
                'google_id' => $googleUser['sub'],
                'password' => bcrypt(Str::random(16)), // random password
            ]);
        }

        // Generate JWT
        $jwt = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $jwt,
            'user' => $user
        ]);
    }
}
