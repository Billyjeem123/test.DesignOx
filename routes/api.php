<?php

use App\Http\Controllers\Client\PostJob\JobController;
use App\Http\Controllers\Client\User\AuthController;
use App\Http\Controllers\Role\RoleController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return auth()->user();
});

// Roles Routes
Route::resource('roles', RoleController::class)->only(['index', 'store']);
/**
 * Authentication routes.
 */

Route::prefix('client')->group(function () {
    #Client authorization Endpoint
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOTP']); #sww
        Route::post('/save-country', [AuthController::class, 'saveUserCountry']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('google-signup', [AuthController::class, 'googleRedirect'])->name('google.redirect');
        Route::post('forget-password', [AuthController::class, 'forgetPassword']);
        Route::get('google-callback', [AuthController::class, 'googleCallBack'])->name('google.callback');
        Route::post('/get-security-question', [AuthController::class, 'getSecurityQuestion'])->name('question.get');

    });
    #Post Job client1...
    Route::middleware(['auth:sanctum', 'client'])->group(function () {
        Route::post('/post-job', [JobController::class, 'createJob'])->name('postJobPayment');
        Route::get('/get-client-jobs/{usertoken}/{on_going?}', [JobController::class, 'getClientJobPosting'])->name('client.getjobs');
        Route::get('/get-job-by-id/{usertoken}/{job_post_id}', [JobController::class, 'getJobById'])->name('client.view_job');
        Route::patch('/update-job-by-id/{job_post_id}', [JobController::class, 'updateJobById'])->name('client.update_job');
        # Profile Endpoint...

        Route::prefix('profile')->group(function () {
            Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');
            Route::post('/set-security-question', [AuthController::class, 'enableSecurityQuestion'])->name('security.set');
            Route::patch('/manage-security-question', [AuthController::class, 'manageSecurityQuestion'])->name('security.manage');



        });


    });





});

Route::fallback(function () {
    return new JsonResponse([
        'message' => 'API resource not found',
        'status_code' => 404
    ], 404);
});
