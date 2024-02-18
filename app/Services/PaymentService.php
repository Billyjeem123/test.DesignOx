<?php

namespace App\Services;
use App\Helpers\Utility;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


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

                ],
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer sk_test_755a02d78f2777eb99ac6ba0b69d8a729b4e1992', #   Replace with your actual secret key
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



    public function handleGatewayCallback(Request $request)
    {
        try {
            $transactionId = request('reference'); #    Assuming you receive the transaction ID in the request

            #    Construct the endpoint URL with the transaction ID
            $url = 'https://api.paystack.co/transaction/verify/' . $transactionId;

            #    Send a GET request to fetch transaction details
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. getenv('PAYSTACK_SECRET_KEY'), #    Replace with your actual secret key
                'Content-Type' => 'application/json',
            ])->get($url);

            #    Check if the request was successful
            if ($response->successful()) {
                #    Get the response data
                $responseData = $response->json();

                echo json_encode($responseData);

                #    Process the response data as needed
                #    For example, you can extract relevant information from $responseData and return it
                return Utility::outputData(true, "Transaction details retrieved successfully", $responseData, 200);
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

}