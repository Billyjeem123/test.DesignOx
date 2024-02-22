<?php
namespace App\Services;
use App\Helpers\Utility;
use App\Mail\adminJobNotify;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function processPayment(array $data)
    {
        try {
            #   Send a POST request to Paystack's initialize transaction endpoint

            $paymentDetails = [
                'amount' => $data['budget'], #   Amount in kobo
                'email' => Auth::user()->email,
                'metadata' => [
                    'usertoken' => Auth::user()->id,
                    'project_desc' => $data['project_desc'],
                    'project_type' => $data['project_type'],
                    'tools_used' => $data['tools_used'],
                    'duration' => $data['duration'],
                    'experience_level' => $data['experience_level'],
                    'budget' => $data['budget'],
                    'numbers_of_proposals' => $data['numbers_of_proposals'],
                    'project_link_attachment' => $data['project_link_attachment'],
                    'payment_channel' => $data['payment_channel'],
                    'keywords' => $data['keywords'],

                ],
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' .config('services.paystack.secrete_key'), // Replace with your actual secret key
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', $paymentDetails);

            #   Check if the request was successful
            if ($response->successful()) {
                #   Get the authorization URL from the response
                $authorizationUrl = $response['data']['authorization_url'];

                #   Return the authorization URL
                return Utility::outputData(true, "Paystack Authorization Url", ['paystack_url' =>$authorizationUrl], 200);
            } else {
                #   Log the error response
                $errorResponse = $response->json();
                $error = 'Paystack API error: ' . json_encode($errorResponse);

                #   Handle the case where the request was not successful
                return Utility::outputData(false, 'Failed to initialize payment.', $error, 200);
            }
        } catch (\Exception $e) {
            #   Handle exceptions
            return Utility::outputData(false, 'An error occurred while initializing payment.' . $e->getMessage(), [], 500);
        }
    }



    public function handleGatewayCallback($transactionId)
    {
        try {
            #    Construct the endpoint URL with the transaction ID
            $url = 'https://api.paystack.co/transaction/verify/' . $transactionId;

            #    Send a GET request to fetch transaction details
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' .config('services.paystack.secrete_key'),
                'Content-Type' => 'application/json',
            ])->get($url);

            #    Check if the request was successful
            if ($response->successful()) {
                #    Get the response data
                $tranxRecord = $response->json();

                $amount = $tranxRecord['data']['amount'];
                $currency = $tranxRecord['data']['currency'];
                $usertoken = $tranxRecord['data']['metadata']['usertoken'];
                $project_desc = $tranxRecord['data']['metadata']['project_desc'];
                $project_type = $tranxRecord['data']['metadata']['project_type'];
                $tools_used = $tranxRecord['data']['metadata']['tools_used'];
                $duration = $tranxRecord['data']['metadata']['duration'];
                $experience_level  = $tranxRecord['data']['metadata']['experience_level'];
                $budget = $tranxRecord['data']['metadata']['budget'];
                $numbers_of_proposals = $tranxRecord['data']['metadata']['numbers_of_proposals'];
                $project_link_attachment = $tranxRecord['data']['metadata']['project_link_attachment'];
                $payment_channel = $tranxRecord['data']['metadata']['payment_channel'];
                $email  = $tranxRecord['data']['customer']['email'];
                $keywords = $tranxRecord['data']['metadata']['keywords'];
                $reference = $tranxRecord['data']['reference'];


                $saveClientJob = Job::create([
                    'client_id' => $usertoken,
                    'project_desc' => $project_desc,
                    'project_type' => $project_type,
                    'tools_used' => $tools_used,
                    'budget' => $budget,
                    'duration' => $duration,
                    'experience_level' => $experience_level,
                    'numbers_of_proposals' => $numbers_of_proposals,
                    'project_link_attachment' => $project_link_attachment

                ]);
                $newlyCreatedJobId = $saveClientJob->id;

                $this->saveJobPaymentTranx($newlyCreatedJobId, $reference, $email, $amount, $currency, $payment_channel);
                $this->saveJobPostingKeyWords($newlyCreatedJobId, $keywords);
                $this->saveJobPostingTools($newlyCreatedJobId,$tools_used);



                Mail::to(config('services.app_config.app_mail'))->send(new adminJobNotify());

                return Utility::outputData(true, "Payment successful", [], 200);
            } else {
                #    Log the error response
                $errorResponse = $response->json();
                $error = 'Paystack API error: ' . json_encode($errorResponse);

                #    Handle the case where the request was not successful
                return Utility::outputData(false, 'Failed to retrieve transaction details.', $error, 200);
            }
        } catch (\Exception $e) {
            #    Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing the request: ' . $e->getMessage(), [], 500);
        }
    }


    public function saveJobPaymentTranx($jobPostingId,$reference, $email, $amount,$currency, $payment_channel): void
    {

        DB::table('tbljob_post_tranx')->insert([
            'job_post_id' => $jobPostingId,
            'reference' => $reference,
            'email' => $email,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'success',
            'payment_channel' => $payment_channel
        ]);
    }



    public function getPaymentTransactionsByJobPostingId($jobPostingId)
    {
        return DB::table('tbljob_post_tranx')
            ->where('job_post_id', $jobPostingId)
            ->get();
    }



    public function saveJobPostingKeyWords(int $jobPostingId, array $keywords): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($keywords as $keyword) {
            $data[] = [
                'job_post_id' => $jobPostingId,
                'keywords' => $keyword,
            ];
        }

        // Insert data into the table
        DB::table('tbljob_keywords')->insert($data);
    }



    public function saveJobPostingTools(int $jobPostingId, array $tools): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($tools as $tool) {
            $data[] = [
                'job_post_id' => $jobPostingId,
                'tools' => $tool,
            ];
        }

        // Insert data into the table
        DB::table('tbljob_tools')->insert($data);
    }


    public function getJobPostingKeyWords($jobPostingId)
    {
        return DB::table('tbljob_keywords')
            ->where('job_post_id', $jobPostingId)
            ->get();
    }







}