<?php

namespace App\Http\Controllers\Client\User;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\UserService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            $user = $this->userService->registerUser($validatedData);

            # Send email verification notification
            Mail::to($validatedData['email'])->send(new WelcomeEmail($validatedData['firstname']));

            return Utility::outputData(true, 'User created successfully.', new UserResource($user), 201);
        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed', $e->getMessage(), 422);
        }
    }


    public function VerifyOTP(UserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $verifyResult = $this->userService->verifyOTP($validatedData);

            return Utility::outputData($verifyResult['success'], $verifyResult['message'],  $verifyResult['data'] ?? [], $verifyResult['status']);
        } catch (\Exception $e) {
            return Utility::outputData(false, $e->getMessage(), [], 500);
        }
    }

    public function login(UserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $loginResult = $this->userService->login($validatedData);

            return Utility::outputData($loginResult['success'], $loginResult['message'],  $loginResult['data'] ?? [], $loginResult['status']);
        } catch (\Exception $e) {
            return Utility::outputData(false, $e->getMessage(), [], 500);
        }
    }



    public function saveUserCountry(UserRequest $request){

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


    public function googleCallBack(): string
     {
         $googleUser = Socialite::driver('google')->stateless()->user();
         $validatedData = [
             'firstname' => $googleUser->user['given_name'],
             'lastname' => $googleUser->user['family_name'],
             'email' => $googleUser->email,
             'password' => Hash::make('google') # default password google
         ];

         $result = $this->userService->registerUserViaGoogle($validatedData);


         return Utility::outputResult($result['success'], $result['message'], new UserResource($result['data']), $result['access_token']);

     }

    public function forgetPassword(UserRequest $request)
    {

        try {

            $validatedData = $request->validated();

            $token = Utility::token();
            $email = $validatedData['email'];

            $result = $this->userService->forgetPassword($email);


            Mail::to($validatedData['email'])->send(new forgetPassword($validatedData['firstname']));

            return Utility::outputData($result['success'], $result['message'], [], 422);

        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed', [], 422);
        }
    }
}
