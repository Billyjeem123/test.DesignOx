<?php

namespace App\Http\Controllers\Client\User;

use App\Events\UserRegistered;
use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Jobs\authJob;
use App\Mail\forgetPassword;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\UserService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request)
    {
        try {
            # Sanitize password
            $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $token = Utility::token();

            $user = $this->userService->registerUser($validatedData, $token);

            event(new UserRegistered($validatedData['fullname'], $token, $validatedData['email']));

            return Utility::outputData(true, 'User created successfully.', new UserResource($user), 201);
        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed', $e->getMessage(), 422);
        }
    }




    public function VerifyOTP(UserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $verifyResult = $this->userService->verifyOTP($validatedData);

            return Utility::outputData($verifyResult['success'], $verifyResult['message'],  $verifyResult['data'] ?? [], $verifyResult['status']);
        } catch (\Exception $e) {
            return Utility::outputData(false, $e->getMessage(), [], 500);
        }
    }

    public function login(UserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $loginResult = $this->userService->login($validatedData);

            return Utility::outputData($loginResult['success'], $loginResult['message'],  $loginResult['data'] ?? [], $loginResult['status']);
        } catch (\Exception $e) {
            return Utility::outputData(false, $e->getMessage(), [], 500);
        }
    }



    public function saveUserCountry(UserRequest $request): \Illuminate\Http\JsonResponse
    {

        try {
            # Sanitize input
            $validatedData = $request->validated();

             $saveCountry  = $this->userService->saveUserCountry($validatedData);

            return Utility::outputData($saveCountry['success'], $saveCountry['message'],  $saveCountry['data'] ??[], $saveCountry['status']);
        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed', $e->getMessage(), 422);
        }



    }

     public function logout()
 {
     try {
         $logout = auth()->user()->tokens()->delete();
         if ($logout) {
             return Utility::outputData(true, "Logged out successfully", [], 200);
         }
     } catch (\Exception $e) {
         return Utility::outputData(false, $e->getMessage(), [], 500);
     }
 }

    public function googleRedirect()
    {
        return   Socialite::driver('google')->stateless()->redirect();

    }


    public function googleCallBackClient(): string
     {
         $googleUser = Socialite::driver('google')->stateless()->user();
         $validatedData = [
             'fullname' => $googleUser->name,
             'phone_number' => 0 ,
             'email' => $googleUser->email,
             'password' => Hash::make('google'), # default password google
             'google_id' => $googleUser->id
         ];

         $result = $this->userService->registerUserViaGoogle($validatedData);

         return Utility::outputData($result['success'], $result['message'], new UserResource($result['data']), $result['status_code']);

     }


    public function forgetPassword(UserRequest $request): \Illuminate\Http\JsonResponse

    {
        try {

            $validatedData = $request->validated();

            $email = $validatedData['email'];

            $result = $this->userService->forgetPassword($email);

            return Utility::outputData($result['success'], $result['message'], new UserResource($result['data']), 200);

        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed'. $e->getMessage(), [], 422);
        }
    }


    public function updatePassword(UserRequest $request): \Illuminate\Http\JsonResponse

    {
        try {
            $validatedData = $request->validated();

            $data = [
                'usertoken' => $validatedData['usertoken'],
                'new_password' => $validatedData['new_password'],
                'old_password' => $validatedData['old_password'],
            ];

           return  $this->userService->updatePassword($data);

        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed'. $e->getMessage(), [], 422);
        }
    }


    public function enableSecurityQuestion(UserRequest $request): array
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'user_id'=> $validatedData['usertoken'],
                'question'=> $validatedData['question'],
                'answer'=> $validatedData['answer'],
                'is_activated' => 1
            ];

            # process table to process security
            return $this->userService->processSecurityQuestion($data);

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to insert data: ' . $e->getMessage(), 'status' => 500];
        }
    }



    public function manageSecurityQuestion(UserRequest $request): array
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'usertoken'=> $validatedData['usertoken'],
                'is_activated' => $validatedData['is_activated']
            ];

            # Process the disablement of security question
           return  $this->userService->manageSecurityQuestion($data);

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getFile(), 'status' => 500];
        }
    }


    public function getSecurityQuestion(UserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'usertoken'=> $validatedData['usertoken'],
                'answer' => $validatedData['answer'] ?? ''
            ];

            # Process the verification of security question
            return $this->userService->getSecurityQuestions($data['usertoken'],$data['answer']);

        } catch (\Exception $e) {
            return Utility::outputData(false, "Unable to process". $e->getMessage(), [], 500);
        }
    }


    public function googleRedirectTalent()
    {
        return Socialite::driver('google')->stateless()->redirect(config('services.google.redirect_talent'));
//      # client sanctum 1|M9JDbKz41RsdmrwravP8hCWfT8uHUfOiDCiGxWGy8bdcca00
    }



}
