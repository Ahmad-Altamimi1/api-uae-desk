<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle the login request and return a token response.
     */
    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentication"},
     *     summary="User Login",
     *     description="Logs the user in after validating credentials and checks the login zone",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "latitude", "longitude"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="latitude", type="number", format="float", example="51.5074"),
     *             @OA\Property(property="longitude", type="number", format="float", example="0.1278"),
     *             @OA\Property(property="remember", type="string", example="on")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Welcome!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Credentials Mismatch!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account deactivated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Your account is Deactivated by Admin!")
     *         )
     *     )
     * )
     */

    // public function login(Request $request)
    // {
    //     $setting = Setting::find(1);
    //     $allowed_login_distance = $setting->allowed_login_distance;

    //     // Define validation rules
    //     $rules = [
    //         'email'     => 'required|email|max:255',
    //         'password'  => 'required',
    //         'latitude'  => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //         'remember'  => 'nullable',
    //     ];

    //     // Validation messages
    //     $messages = [
    //         'email.required'    => __('auth.form.validation.email.required'),
    //         'email.email'       => __('auth.form.validation.email.email'),
    //         'password.required' => __('auth.form.validation.email.required'),
    //     ];

    //     // Validate incoming request
    //     $data = $request->validate($rules, $messages);
    //     $data['remember'] = $data['remember'] ?? "off"; // Default to 'off' if remember is not provided

    //     // Authenticate the user
    //     if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember'])) {
    //         $user = Auth::user();

    //         if ($user->status != 1) {
    //             Auth::logout();
    //             return response()->json(['error' => 'Account deactivated'], 403);
    //         }

    //         // If user is not flexible and not admin, check branches and location
    //         if (!$user->is_location_flexible && !($user->hasRole('Admin') || $user->hasRole('Super Admin'))) {
    //             $branches = $user->branches;

    //             if ($branches->isEmpty()) {
    //                 Auth::logout();
    //                 return response()->json(['error' => 'No branches assigned'], 403);
    //             }

    //             $withinAllowedZone = false;
    //             $loginTime = Carbon::now()->addHours(4);
    //             $shift = $user->shift;
    //             $shiftStartTime = Carbon::today()->setTimeFromTimeString($shift->start_time);

    //             $lateMinutes = $loginTime->diffInMinutes($shiftStartTime);
    //             $isLate = $loginTime->greaterThan($shiftStartTime);
    //             foreach ($branches as $branch) {
    //                 if (!$branch->latitude || !$branch->longitude) {
    //                     continue;
    //                 }

    //                 $distance = $this->calculateDistance(
    //                     $request->latitude,
    //                     $request->longitude,
    //                     $branch->latitude,
    //                     $branch->longitude
    //                 );

    //                 if ($distance <= $allowed_login_distance) {
    //                     $withinAllowedZone = true;
    //                     // Record attendance
    //                     $attendanceData = [
    //                         'login_time'   => now()->timezone('Asia/Dubai')->format('Y-m-d H:i:s'),
    //                         'branch_id'    => $branch->id,
    //                     ];

    //                     if ($isLate) {
    //                         $attendanceData['is_late'] = true;
    //                         $attendanceData['late_minutes'] = $lateMinutes;
    //                     } else {
    //                         $attendanceData['is_late'] = false;
    //                         $attendanceData['late_minutes'] = 0;
    //                     }

    //                     $user->attendances()->create($attendanceData);
    //                     break;
    //                 }
    //             }

    //             if (!$withinAllowedZone) {
    //                 Auth::logout();
    //                 return response()->json(['error' => 'Not within allowed zone'], 403);
    //             }
    //         }

    //         // Return response with redirect
    //         if ($user->hasRole('operator')) {
    //             return response()->json(['redirect' => route('customers.index')]);
    //         } elseif ($user->hasRole('supervisor') || $user->hasRole('expert')) {
    //             return response()->json(['redirect' => route('branches.allBranchesData')]);
    //         }

    //         return response()->json(['redirect' => route('admin.dashboard')]);
    //     } else {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }
    // }
    public function login(Request $request)
    {
        $setting = Setting::find(1);
        $allowed_login_distance = $setting->allowed_login_distance;

        // Define validation rules
        $rules = [
            'email'     => 'required|email|max:255',
            'password'  => 'required',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'remember'  => 'nullable',
        ];

        // Validation messages
        $messages = [
            'email.required'    => __('auth.form.validation.email.required'),
            'email.email'       => __('auth.form.validation.email.email'),
            'password.required' => __('auth.form.validation.email.required'),
        ];
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
 // Validate incoming request
 $data = $request->validate($rules, $messages);
 $data['remember'] = $data['remember'] ?? "off"; // Default to 'off' if remember is not provided

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if ($user->status != 1) {
                Auth::logout();
                return response()->json(['error' => 'Account deactivated'], 600);
            }
            if (!$user->is_location_flexible && !($user->hasRole('Admin') || $user->hasRole('Super Admin'))) {
                $branches = $user->branches;

                if ($branches->isEmpty()) {
                    Auth::logout();
                    return response()->json(['error' => 'No branches assigned'], 600);
                }

                $withinAllowedZone = false;
                $loginTime = Carbon::now()->addHours(4);
                $shift = $user->shift;
                $shiftStartTime = Carbon::today()->setTimeFromTimeString($shift->start_time);

                $lateMinutes = $loginTime->diffInMinutes($shiftStartTime);
                $isLate = $loginTime->greaterThan($shiftStartTime);
                foreach ($branches as $branch) {
                    if (!$branch->latitude || !$branch->longitude) {
                        continue;
                    }

                    $distance = $this->calculateDistance(
                        $request->latitude,
                        $request->longitude,
                        $branch->latitude,
                        $branch->longitude
                    );

                    if ($distance <= $allowed_login_distance) {
                        $withinAllowedZone = true;
                        // Record attendance
                        $attendanceData = [
                            'login_time'   => now()->timezone('Asia/Dubai')->format('Y-m-d H:i:s'),
                            'branch_id'    => $branch->id,
                        ];

                        if ($isLate) {
                            $attendanceData['is_late'] = true;
                            $attendanceData['late_minutes'] = $lateMinutes;
                        } else {
                            $attendanceData['is_late'] = false;
                            $attendanceData['late_minutes'] = 0;
                        }

                        $user->attendances()->create($attendanceData);
                        break;
                    }
                }

                if (!$withinAllowedZone) {
                    Auth::logout();
                    return response()->json(['error' => 'Not within allowed zone'], 600);
                }
            }
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




    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Revoke the user's token
            $user->tokens->each(function ($token) {
                $token->delete();
            });
        }

        // Log out the user and invalidate session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Helper function to calculate the distance between two coordinates.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; // Distance in km
    }
}
