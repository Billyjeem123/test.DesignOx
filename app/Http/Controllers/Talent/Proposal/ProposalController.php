<?php

namespace App\Http\Controllers\Talent\Proposal;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Models\Job;
use App\Models\Proposal;
use App\Services\ProposalService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProposalController extends Controller
{

    private ProposalService $proposalService;

    public function __construct(ProposalService $proposalService)
    {
        $this->proposalService = $proposalService; #  Inject ProposalService instance

    }


    public function sendClientProposal(JobRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = auth('api')->user();

            $this->authorize('create', [Proposal::class]);

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
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }
}
