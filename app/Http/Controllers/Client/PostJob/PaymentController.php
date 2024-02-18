<?php

namespace App\Http\Controllers\Client\PostJob;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Facades\Http;
class PaymentController extends Controller
{


    public function initiatePayment(Request $request)
    {
        try {
            // Process your payment initiation logic here
            // Example:
            $paymentDetails = [
                'amount' => 100, // Amount in kobo
                'email' => 'sam@example.com', // Customer's email
                'metadata' => [
                    'order_id' => '123456',
                ],
            ];

            // Send a POST request to Paystack's initialize transaction endpoint
            $response = Http::withHeaders([
                'Authorization' => 'Bearer sk_test_755a02d78f2777eb99ac6ba0b69d8a729b4e1992', // Replace with your actual secret key
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', $paymentDetails);

            // Check if the request was successful
            if ($response->successful()) {
                // Get the authorization URL from the response
                $authorizationUrl = $response['data']['authorization_url'];

                // Redirect the user to the authorization URL
   return   Utility::outputData(true, "Paystack Autorization Url", $authorizationUrl, 200);
            } else {
                // Log the error response
                $errorResponse = $response->json();
                $error = 'Paystack API error: ' . json_encode($errorResponse);

                // Handle the case where the request was not successful
                return Utility::outputData(false, 'Failed to initialize payment.',  $error,  200);
            }
        } catch (\Exception $e) {
            return Utility::outputData(false, 'An error occurred while initializing payment.' . $e->getMessage(), [], 200);
        }
    }






    public function handlePaymentCallback(Request $request)
    {
        // Process your payment callback logic here
        // Example:
        $paymentData = Paystack::getPaymentData();
        var_dump($paymentData);

        // Do something with $paymentData (e.g., update order status, log payment details, etc.)

        // Redirect or return a response to the client
//        return redirect()->route('home')->with('success', 'Payment successful!');
    }
}
