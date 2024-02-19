<?php

namespace App\Http\Controllers\Client\PostJob;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Services\PaymentService;
use App\Helpers\Utility;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    protected $paymentService;

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



    public function payForJobPosting(){

        $transactionId = request('reference'); #   received the transaction ID in the request

        return $this->paymentService->handleGatewayCallback($transactionId);

    }





}


