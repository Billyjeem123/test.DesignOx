<?php

namespace App\Services;

use App\Helpers\Utility;
use App\Http\Resources\ProposalResource;
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
                'talent_fullname' => auth()->user()->fullname,
                'proposal_cover_info' => strtok(wordwrap($data['cover_letter'], 50), "\n") . '...',  #shorten the cover letter
                'link_to_proposal' => config('services.app_config.app_proposal_url')  . '/' . $job->id ,
            ];

            Mail::to($job->user->email)->send(new clientNotifyProposal($credential));

            return Utility::outputData(true, "Proposal sent successfully", [], 201);

        } catch (\Exception $e) {
            # Handle exceptions

            return Utility::outputData(false, 'An error occurred while processing job posting.' . $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }

    public function getJobProposals(int $jobid): \Illuminate\Http\JsonResponse
    {
        $job = Job::find($jobid);

        if (is_null($job)) {
            return Utility::outputData(false, "No records found", [], 404);
        }
        $allProposals = $job->proposals()->orderByDesc('created_at')->get();
        
        # Return paginated response
        return  Utility::outputData(true, "Proposals fetched  successfully", new ProposalResource($allProposals), 200);

    }

    public function deleteProposal(int $proposal_id): \Illuminate\Http\JsonResponse
    {
        try {

            $affectedRows = Proposal::where('id', $proposal_id)->delete();

            if ($affectedRows === 0) {
                # No proposal was found with the given ID
                return Utility::outputData(false, "proposal not found", [], 404);
            }

            return Utility::outputData(true, "Proposal deleted successfully", [], 200);

        } catch (\Exception $e) {
            throw new \Exception('An error occurred while deleting job and related data: ' . $e->getMessage(), Utility::getExceptionDetails($e));
        }
    }


}