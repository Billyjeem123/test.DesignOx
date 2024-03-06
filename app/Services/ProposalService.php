<?php

namespace App\Services;

use App\Helpers\Utility;
use App\Mail\adminJobNotify;
use App\Mail\clientNotifyProposal;
use App\Models\Job;
use App\Models\Proposal;
use Illuminate\Support\Facades\Mail;

class ProposalService
{

    public function processTalentOffer(array $data): \Illuminate\Http\JsonResponse
    {
        try {
            # Process client jobs postings..

            Proposal::create($data); #  Corrected line
            $job = Job::findOrFail($data['job_post_id']);

            $credential = [
                'client_fullname' => $job->user->fullname,
                'project_title' => $job->project_title,
                'talent_fullname' => auth()->user->fullname,
                'proposal_cover_info' => strtok(wordwrap($data['cover_letter'], 50), "\n") . '...',  #shorten the cover letter
                'link_to_proposal' => config('services.app_config.app_proposal_url')  . '/' . $job->id ,
            ];

            Mail::to(config($job->user->email))->send(new clientNotifyProposal($credential));

            return Utility::outputData(true, "Proposal sent successfully", [], 201);

        } catch (\Exception $e) {
            # Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing job posting.' . $e->getMessage(), [], 500);
        }
    }

}