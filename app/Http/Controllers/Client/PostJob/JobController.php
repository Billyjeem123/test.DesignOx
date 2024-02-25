<?php

namespace App\Http\Controllers\Client\PostJob;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Helpers\Utility;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    protected  $paymentService;
    public  $tools;
    public  $keywords;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService; #  Inject PaymentService instance
    }

    public function createJob(JobRequest $request)
    {
        try {

            $data = [
                'client_id' => $request['usertoken'],
                'project_desc' => $request['project_desc'],
                'project_type' => $request['project_type'],
                'tools_used' => $request['tools_used'],
                'budget' => $request['budget'],
                'duration' => $request['duration'],
                'experience_level' => $request['experience_level'],
                'numbers_of_proposals' => $request['numbers_of_proposals'],
                'project_link_attachment' => $request['project_link_attachment'],
                'payment_channel' => $request['payment_channel'],
                'keywords' => $request['keywords']
            ];

            return $this->paymentService->processPayment($data);


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


