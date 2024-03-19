<?php

namespace App\Http\Controllers\Talent\Reviews;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Reviews;
use App\Services\ReviewService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewsController extends Controller
{

    public  ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService; #  Inject reviewService instance

    }
    public function Makereview(ReviewRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $user = auth('api')->user();

             $this->authorize('create', Reviews::class);

            $data = [
                'user_id' => $user->id,
                'job_design_id' => $validatedData['job_design_id'],
                'reviews' => $validatedData['review'],
                'ratings' => $validatedData['ratings'],
            ];

            return $this->reviewService->processReview($data);

        } catch (ValidationException $e) {
            #  Return validation errors
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            #  Handle any other exceptions that may occur during role creation
            return Utility::outputData(false, "An error occurred", $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }



    public function getDesignReviews($designId): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->reviewService->getDesignReviews($designId);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access",  $e->getMessage(), 404);
        }
    }


    public function deleteReview(ReviewRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'review_id' => $validatedData['review_id'],
            ];

            $this->authorize('delete', Reviews::findOrFail($data['review_id']));

            return $this->reviewService->deleteReview($data['review_id']);


        } catch (ValidationException $e) {
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }
}
