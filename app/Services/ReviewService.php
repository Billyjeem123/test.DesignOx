<?php

namespace App\Services;


use App\Events\NotifyReview;
use App\Helpers\Utility;
use App\Http\Resources\ReviewResource;
use App\Models\Design;
use App\Models\Reviews;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class  ReviewService
{

    public function processReview($data): \Illuminate\Http\JsonResponse
    {
        try {
            # Process reviews jobs postings..
            Reviews::create($data); # Corrected line

            $design =  Design::findOrFail($data['job_design_id']);

            $credential = [
                'commentor_name' => auth()->user()->fullname,
                'author' => $design->user->fullname,
                'project_title' => $design->project_title,
                'author_email' => $design->user->email,
                'link_to_review' => $this->generateReviewLink($design->user->id, $design->id),
            ];

            event(new NotifyReview($credential));

            return Utility::outputData(true, "Review sent...", [], 201);

        } catch (\Exception $e) {
            # Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing design reviews.' . $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }


    private function generateReviewLink($userid, $designid): ?string
    {

        $user = User::find($userid);

        if (!$user->hasRole('client')) {
            # If the user does not have the 'client' role, return null
            return null;
        }

        # If the user has the 'client' role, generate the review link based on the role
        if ($user->hasRole('client')) {
            $linkToReview = config('services.app_config.client_review_link_url') . '/' . $designid;
        } elseif ($user->hasRole('talent')) {
            $linkToReview = config('services.app_config.talent_review_link_url') . '/' . $designid;
        } else {
            # Handle the case when the user's role is unknown or unsupported, which likely not possible, but still... we gotta run it.
            return null;
        }

        return $linkToReview;
    }


    public function getDesignReviews(int $designId): \Illuminate\Http\JsonResponse
    {
        $design = Design::find($designId);

        if (is_null($design)) {
            return Utility::outputData(false, "No records found", [], 404);
        }
        $allReviews = $design->reviews()->orderByDesc('created_at')->get();

        # Return paginated response
        return  Utility::outputData(true, "Reviews fetched  successfully", new ReviewResource($allReviews), 200);

    }



    public function deleteReview(int $review_id): \Illuminate\Http\JsonResponse
    {
        try {
            # Delete job details
            $affectedRows = Reviews::where('id', $review_id)->delete();

            if ($affectedRows === 0) {
                # No review  was found with the given ID
                return Utility::outputData(false, "Job not found", [], 404);
            }

            return Utility::outputData(true, "Review deleted successfully", [], 200);

        } catch (\Exception $e) {
            throw new \Exception('An error occurred while deleting review data: ' . $e->getMessage());
        }
    }

}