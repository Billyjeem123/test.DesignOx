<?php

use App\Http\Controllers\Client\PostJob\JobController;
use App\Http\Controllers\Client\User\AuthController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Talent\Designs\DesignController;
use App\Http\Controllers\Talent\Proposal\ProposalController;
use App\Http\Controllers\Talent\Reviews\ReviewsController;
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
        Route::get('google-callback', [AuthController::class, 'googleCallBackClient'])->name('google.callback');
        Route::post('forget-password', [AuthController::class, 'forgetPassword']);
        Route::post('/get-security-question', [AuthController::class, 'getSecurityQuestion'])->name('question.get');

    });
    #Post Job client1...
    Route::middleware(['auth:sanctum','client'])->group(function () {
        Route::post('/post-job', [JobController::class, 'createJob'])->name('postJobPayment');
        Route::get('/get-client-jobs/{on_going?}', [JobController::class, 'getClientJobPosting'])->name('client.getjobs');
        Route::get('/get-job-by-id/{job_post_id}', [JobController::class, 'getJobById'])->name('client.view_job');
        Route::patch('/update-job-by-id/{job_post_id}', [JobController::class, 'updateJobById'])->name('client.update_job');
        Route::delete('/delete-job-by-id', [JobController::class, 'deleteJobById'])->name('client.delete_job');
        Route::get('/get-job-proposal/{job_post_id}', [ProposalController::class, 'getJobProposal'])->name('proposal.get');
        # Profile Endpoint...
 
        Route::prefix('profile')->group(function () {
            Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');
            Route::post('/set-security-question', [AuthController::class, 'enableSecurityQuestion'])->name('security.set');
            Route::patch('/manage-security-question', [AuthController::class, 'manageSecurityQuestion'])->name('security.manage');
        });

        # Designs AND Reviews Endpoint...
        Route::prefix('designs')->group(function () {
            Route::post('/get-all-designs', [DesignController::class, 'getAllDesigns'])->name('get.all.design');
            Route::post('/save-design', [DesignController::class, 'saveDesign'])->name('save.design');
            Route::get('/get-saved-designs', [DesignController::class, 'getSavedDesigns'])->name('get.saved.design');
            Route::post('/like-design', [DesignController::class, 'likeDesign'])->name('like.design');
            Route::get('/get-design-by-id/{id}', [DesignController::class, 'getDesignsById'])->name('get.saved.design.id');
            Route::post('/review', [ReviewsController::class, 'Makereview'])->name('create.review');
            Route::get('/get-design-reviews/{id}', [ReviewsController::class, 'getDesignReviews'])->name('get.reviews.design.id');
        });


    });

});

#Talent Endpoint[Registration]
Route::prefix('talent')->group(function () {
    # Talent authorization Endpoint
    Route::prefix('auth')->group(function () {
        Route::get('/google-signup', [AuthController::class, 'googleRedirectTalent'])->name('talent.google.redirect');

    });
#Talent Endpoint[PROPOSAL, GET JOBS, SAVE JOBS]
    Route::middleware(['auth:sanctum','talent'])->group(function () {
        Route::post('get-all-jobs', [JobController::class, 'getAllJobPosting'])->name('get.all.jobs.talent');
        Route::get('/get-job-by-id/{job_post_id}', [JobController::class, 'getSpecificJobById'])->name('talent.view_job');
        Route::get('get-related-jobs/{job_post_id}', [JobController::class, 'viewRelatedJobs'])->name('get.related.jobs');
        Route::post('/save-job', [JobController::class, 'saveJob'])->name('save.job');
        Route::get('/get-saved-jobs', [JobController::class, 'getSavedJobs'])->name('get.saved.jobs');
        Route::delete('/delete-saved-jobs', [JobController::class, 'deleteSavedJobs'])->name('delete.saved.jobs');
        Route::post('/send-proposal', [ProposalController::class, 'sendClientProposal'])->name('proposal.send');
        Route::get('/get-job-proposal/{job_post_id}', [ProposalController::class, 'getJobProposal'])->name('proposal.get');
        Route::delete('/delete-job-proposal/{job_post_id}', [ProposalController::class, 'deleteJobProposal'])->name('proposal.delete');



        Route::prefix('designs')->group(function () {
            Route::post('/upload-design', [DesignController::class, 'uploadDesign'])->name('talent.upload.design');
            Route::post('/get-all-designs', [DesignController::class, 'getAllDesigns'])->name('get.all.design');
            Route::post('/save-design', [DesignController::class, 'saveDesign'])->name('save.design');
            Route::get('/get-saved-designs', [DesignController::class, 'getSavedDesigns'])->name('get.saved.design');
            Route::post('/like-design', [DesignController::class, 'likeDesign'])->name('like.design');
            Route::get('/get-user-designs', [DesignController::class, 'getTalentDesigns'])->name('get.talent.designs');
            Route::get('/get-design-by-id/{id}', [DesignController::class, 'getDesignsById'])->name('get.saved.design.id');
            Route::post('/review', [ReviewsController::class, 'Makereview'])->name('create.review');
            Route::get('/get-design-reviews/{id}', [ReviewsController::class, 'getDesignReviews'])->name('get.reviews.design.id');
        });


        # Profile Endpoint...

        Route::prefix('profile')->group(function () {
            Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');
            Route::post('/set-security-question', [AuthController::class, 'enableSecurityQuestion'])->name('security.set');
            Route::patch('/manage-security-question', [AuthController::class, 'manageSecurityQuestion'])->name('security.manage');
            Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        });


    });

});


Route::fallback(function () {
    return new JsonResponse([
        'message' => 'API resource not found',
        'status_code' => 404
    ], 404);
});
