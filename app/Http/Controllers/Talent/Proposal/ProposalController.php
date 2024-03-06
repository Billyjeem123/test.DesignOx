<?php

namespace App\Http\Controllers\Talent\Proposal;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Requests\ProposalRequest;
use App\Http\Resources\JobResource;
use App\Http\Resources\ProposalResource;
use App\Models\Job;
use App\Models\Proposal;
use App\Services\ProposalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ProposalController extends Controller
{

    private ProposalService $proposalService;

    public function __construct(ProposalService $proposalService)
    {
        $this->proposalService = $proposalService; #  Inject ProposalService instance

    }

    public function sendClientProposal(ProposalRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $user = auth('api')->user();

            $this->authorize('create', Proposal::class);

            $data = [
                'talent_id' => $user->id,
                'job_post_id' => $validatedData['job_post_id'],
                'attachment' => $validatedData['attachment'],
                'cover_letter' => $validatedData['cover_letter'],
                'preferred_date' => $validatedData['preferred_date'],
            ];

            return $this->proposalService->processTalentOffer($data);

        } catch (ValidationException $e) {
            #  Return validation errors
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            #  Handle any other exceptions that may occur during role creation
            return Utility::outputData(false, "An error occurred", $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }



    public function getJobProposal(int $jobid): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->proposalService->getJobProposals($jobid);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }

    public function deleteJobProposal(int $proposal_id): \Illuminate\Http\JsonResponse
    {
        try {
            # Check if the proposal exists
            $proposal = Proposal::find($proposal_id);

            if (!$proposal) {
                # Proposal not found, return 404 response
                return Utility::outputData(false, "Proposal not found", [], 404);
            }

            # Proposal exists, authorize and delete
            $this->authorize('delete', $proposal);

            return $this->proposalService->deleteProposal($proposal_id);

        } catch (\Exception $e) {
            # Other exceptions
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }

}
