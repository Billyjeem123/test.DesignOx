<?php

use App\Http\Controllers\Client\PostJob\JobController;
use App\Http\Controllers\Client\PostJob\PaymentController;
use App\Http\Controllers\Client\User\AuthController;
use App\Http\Controllers\Role\RoleController;
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

// Roles Routes
Route::resource('roles', RoleController::class)->only(['index', 'store']);


/**
 * Authentication routes.
 */
// Route for initiating payment
Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');

// Route for handling Paystack callback after payment
Route::get('/payment/callback/{reference}', [PaymentController::class, 'handleGatewayCallback'])->name('payment.callback');

Route::prefix('client')->group(function () {

    #Client authorization Endpoint
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOTP']); #sww
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    #Post Job client...
    Route::post('/post-job', [JobController::class, 'createJob'])->name('postJobPayment');



    # Register with google
    Route::get('/google-redirect', [AuthController::class, 'googleRedirect'])->name('googlelogin');
    Route::get('/google-callback', [AuthController::class, 'googleCallBack']);
});

Route::post('/task', [AuthController::class, 'task']);

Route::fallback(function () {
    abort(404, 'API resource not found');
});
