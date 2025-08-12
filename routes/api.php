<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CancerController;
use App\Http\Controllers\BeneficiariesController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\LgaController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\CadreController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HubsController;
use App\Http\Controllers\MSPsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\CohortsController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route::middleware(['cors'])->group(function () {
    // Public routes
    Route::post('/signup', [AuthController::class, 'signup2']);
    Route::post('/signin', [AuthController::class, 'signin']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/users/profile', [AuthController::class, 'profile'])->middleware('auth.jwt'); // Use auth.jwt instead of auth:api

    Route::post('/verify-jamb', [JAMBController::class, 'verifyJAMB']);
    Route::get('/jamb', [JAMBController::class, 'index']);

    Route::get('/courses/available', [CohortsController::class, 'index']);

Route::post('/membership-application', [MembershipController::class, 'store']);
    // Protected routes with JWT authentication
    Route::get('/membership-application', [MembershipController::class, 'index']);
    Route::get('/roles', [RolesController::class, 'index']);

    Route::post('/payment/notify-transfer', [PaymentsController::class, 'notifyTransfer']);
    Route::put('/payments', [PaymentsController::class, 'update']);

    Route::middleware(['auth.jwt'])->group(function () {
        Route::get('/user', function () {
            $user = auth()->user();
            return response()->json([
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'otherNames' => $user->otherNames,
                'email' => $user->email,
                'role' => $user->user_role->roleName,
                'id' => $user->id,
                'message' => 'User authenticated successfully',
            ]);
        });

        // Application routes
    Route::get('/student/courses', [StudentsController::class, 'getStudentCourses']);
    Route::get('/my-payments', [PaymentsController::class, 'index']);

    Route::get('/user/profile', [ProfileController::class, 'getProfile']);
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::delete('/user/profile/picture', [ProfileController::class, 'deleteProfilePicture']);

    // Resource download routes
    Route::get('/resources/download/{resource}', [ResourceController::class, 'download'])
        ->name('resources.download');
        
    Route::get('/resources/stream/{resource}', [ResourceController::class, 'stream'])
        ->name('resources.stream');

});
        
    
Route::get('analytics/total-users', [AnalyticsController::class, 'getTotalBeneficiaries']);

    Route::options('{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

Route::post('/auth/google', [GoogleAuthController::class, 'login']);
