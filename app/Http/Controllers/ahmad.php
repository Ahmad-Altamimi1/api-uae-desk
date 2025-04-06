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

class ahmad extends Controller
{
    //////////////////////
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;

            Log::info('User logged in successfully', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'user' => $user,
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
}
