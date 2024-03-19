<?php

namespace App\Services;


use App\Events\NotifyReview;
use App\Helpers\Utility;
use App\Models\Design;
use App\Models\Reviews;

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
                'link_to_review' => config('services.app_config.review_link_url')  . '/' . $design->id ,
            ];

            event(new NotifyReview($credential));

            return Utility::outputData(true, "Review sent...", [], 201);

        } catch (\Exception $e) {
            # Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing design reviews.' . $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }

}