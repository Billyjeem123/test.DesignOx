<?php

namespace App\Http\Controllers\Client\PostJob;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Helpers\Utility;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    protected  $jobService;
    public  $tools;
    public  $keywords;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService; #  Inject PaymentService instance
    }

    public function createJob(JobRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $data = [
                'client_id' => $validatedData['usertoken'],
                'project_desc' => $validatedData['project_desc'],
                'project_type' => $validatedData['project_type'],
                'tools_used' => $validatedData['tools_used'],
                'budget' => $validatedData['budget'],
                'duration' => $validatedData['duration'],
                'experience_level' => $validatedData['experience_level'],
                'numbers_of_proposals' => $validatedData['numbers_of_proposals'],
                'project_link_attachment' => $validatedData['project_link_attachment'],
                'keywords' => $validatedData['keywords'],
                'job_status' =>0
            ];

            return $this->jobService->processClientJob($data);


        } catch (ValidationException $e) {
            #  Return validation errors
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            #  Handle any other exceptions that may occur during role creation
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }


    public function payForJobPosting()
    {

//        $transactionId = request('reference'); #   received the transaction ID in the request
//
//        return $this->paymentService->handleGatewayCallback($transactionId);

    }

    public function getClientJobPosting(Request $request)
    {
        try {
            $clientId = $request->input('usertoken');
            $onGoing = $request->input('on_going', null);

            // Apply conditional filtering based on the 'on_going' parameter
            $query = Job::where('client_id', $clientId);
            if ($onGoing === '1') {
                $query->where('on_going', 1);
            } elseif ($onGoing === '0') {
                $query->where('on_going', 0);
            }

            # Paginate the results
            $jobsByClient = $query->paginate(10); # Change the number as per your requirement

            if ($jobsByClient->isEmpty()) {
                return Utility::outputData(false, "No results found", [], 404);
            }

            # Fetch tools for each job
            foreach ($jobsByClient as $job) {
                $job->tools = $this->paymentService->getJobPostingTools($job->id);
                $job->keywords = $this->paymentService->getJobPostingKeyWords($job->id);
            }

            # Return paginated response
           return  Utility::outputData(true, "Client jobs fetched  successfully", JobResource::collection($jobsByClient), 200);

        } catch (\PDOException $e) {
            # Handle PDOException
           return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        }
    }





}


