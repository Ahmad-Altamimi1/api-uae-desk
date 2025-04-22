<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //////////////////////
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $permissions = $user->getAllPermissions();
            $permissions = $user->getAllPermissions()->pluck('name');

            $token = $user->createToken('authToken')->accessToken;

            Log::info('User logged in successfully', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'user' => $user,
                'permissions' => $permissions,
                "message" => "User logged in successfully.",
                'access_token' => $token,
            ], 200);
        }

        Log::error('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'status' => 'failed'
        ]);

        return response()->json([
            'message' => 'Invalid login credentials.',
        ], 401);
    }

    public function logout(Request $request)
    {


        // Check if the user is authenticated
        if (!auth()->check()) {
            // Log unauthenticated logout attempt (error level)
            Log::error('Unauthenticated user attempted to logout', [
                'ip' => $request->ip()
            ]);

            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Revoke the user's token
        $request->user()->token()->revoke();

        // Log successful logout attempt (info level)
        Log::info('User logged out successfully', [
            'user_id' => $request->user()->id,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'message' => 'User logged out successfully!'
        ], 200);
    }
}
