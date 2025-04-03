<?php

use App\Http\Controllers\ahmad;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::post('/login', [ahmad::class, 'login']);
// Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['auth:api', 'permission:user-list'])->get('/user', function (Request $request) {
    $user = $request->user();
    $permissions = $user->getAllPermissions();
    return response()->json(['user' => $user, 'permissions' => $permissions]);
});
C:\xampp\htdocs\UAE-desk\backend\routes\api.php