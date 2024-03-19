<?php

namespace App\Services;


use App\Helpers\Utility;
use App\Models\Design;
use App\Models\Reviews;

class  ReviewService
{



    public function processReview(array $data): \Illuminate\Http\JsonResponse
    {
        try {
            # Process reviews jobs postings..

            Reviews::create($data); #  Corrected line

            $job =  Design::findOrFail($data['job_design_id']);

            $credential = [
                'client_fullname' => $job->user->fullname,
                'project_title' => $job->project_title,
                'talent_fullname' => auth()->user()->fullname,
                'client_email' => $job->user->email,
                'proposal_cover_info' => strtok(wordwrap($data['cover_letter'], 50), "\n") . '...',  #shorten the cover letter
                'link_to_proposal' => config('services.app_config.app_proposal_url')  . '/' . $job->id ,
            ];

//            event(new JobProposal($credential));

            return Utility::outputData(true, "Review sent...", [], 201);

        } catch (\Exception $e) {
            # Handle exceptions

            return Utility::outputData(false, 'An error occurred while processing  design reviews.' . $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }
}