<?php

use App\Http\Controllers\ahmad;
use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Haruncpi\LaravelUserActivity\Models\Log;
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
Route::get('/test', function () {
    return response()->json(['message' => User::all()]);
});

Route::post('/login', [ahmad::class, 'login']);
Route::post('/logout', [ahmad::class, 'logout'])->middleware(['auth:api']); 

// Route::post('/login', [LoginController::class, 'login']);
// Route::middleware(['auth:api', 'permission:user-list'])->get('/user', function (Request $request) {
//     $user = $request->user();
//     $permissions = $user->getAllPermissions();
//     return response()->json(['user' => $user, 'permissions' => $permissions]);
// });
Route::post('/customers/media/store', [App\Http\Controllers\Admin\CustomerController::class, 'storeMedia'])->middleware(['auth:api']); 
// ->middleware(['auth:api', 'permission:user-list'])
Route::prefix('customers')->middleware(['auth:api'])->group(function () {
    Route::get('/index', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('customers.create');
    Route::post('/store', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('customers.store');
    Route::get('/edit/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('customers.edit');
    Route::post('/update', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('customers.update');
    Route::post('/destroy', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/view/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
    Route::get('accountStatement/view/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'accountStatement'])->name('customers.account-statement');
    Route::post('/importcustomers', [App\Http\Controllers\Admin\CustomerController::class, 'importCustomers'])->name('customers.import');
    Route::post('/upload/fta-docuemnt/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'storeFtaMedia'])->name('customers.upload.fta_document');
    Route::match(['post', 'put'], '/update/fta-docuemnt/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'updateFtaDocument'])->name('customers.updateFtaDocument');
    Route::get('/media/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'media'])->name('customers.media'); // Media Upload Route
    Route::delete('/media/delete/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'deleteMedia'])->name('customers.media.delete');
    Route::post('/{id}/submit-verification', [App\Http\Controllers\Admin\CustomerController::class, 'submitForVerification'])->name('customers.submit.verification');
    Route::post('/{customer}/submit-review', [App\Http\Controllers\Admin\CustomerController::class, 'submitForReview'])->name('customers.submit.review');
    Route::post('/{customer}/request-document', [App\Http\Controllers\Admin\CustomerController::class, 'requestDocument'])->name('customers.request.document');
    Route::post('/{customer}/add-tax-id', [App\Http\Controllers\Admin\CustomerController::class, 'addTaxId'])->name('customers.add.tax_id');
    Route::post('/customers/edit-status', [App\Http\Controllers\Admin\CustomerController::class, 'editStatus'])->name('customers.edit.status');
    Route::post('/customers/updateCreator', [App\Http\Controllers\Admin\CustomerController::class, 'updateCreator'])->name('customers.edit.creator');
});



// Role
Route::prefix('roles')->middleware(['auth:api'])->group(function () {
    Route::get('/index', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
    Route::get('/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
    Route::post('/store', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
    Route::get('/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/update/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
    Route::post('/destroy', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
});


// Permission
Route::prefix('permissions')->middleware(['auth:api'])->group(function () {
    Route::get('/index', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/create', [App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/store', [App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/edit/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/update/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update');
    Route::post('/destroy', [App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('permissions.destroy');
});
// Service
Route::prefix('services')->middleware(['auth:api'])->group(function () {
    Route::get('/index', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('/create', [App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
    Route::post('/store', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.store');
    Route::get('/edit/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('services.edit');
    Route::post('/update/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('services.update');
    Route::post('/destroy', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');
});
//Branch
Route::prefix('branches')->middleware(['auth:api'])->group(function () {
    Route::get('/index', [App\Http\Controllers\Admin\BranchController::class, 'index'])->name('branches.index');
    Route::get('/create', [App\Http\Controllers\Admin\BranchController::class, 'create'])->name('branches.create');
    Route::post('/store', [App\Http\Controllers\Admin\BranchController::class, 'store'])->name('branches.store');
    Route::get('/edit/{id}', [App\Http\Controllers\Admin\BranchController::class, 'edit'])->name('branches.edit');
    Route::post('/update/{id}', [App\Http\Controllers\Admin\BranchController::class, 'update'])->name('branches.update');
    Route::post('/destroy', [App\Http\Controllers\Admin\BranchController::class, 'destroy'])->name('branches.destroy');
    Route::get('/all-branches', [App\Http\Controllers\Admin\BranchController::class, 'allBranchesData'])->name('branches.allBranchesData');
});

