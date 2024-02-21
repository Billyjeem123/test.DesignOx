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

            return Utility::outputData($verifyResult['success'], $verifyResult['message'],  $verifyResult['data'], $verifyResult['status']);
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

            return Utility::outputData($saveCountry['success'], $saveCountry['message'],  $saveCountry['data'], $saveCountry['status']);
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
        return  $redirectUrl = Socialite::driver('google')->stateless()->redirect();

    }


    public function googleCallBack(): string
     {
         $googleUser = Socialite::driver('google')->stateless()->user();
         $validatedData = [
             'firstname' => $googleUser->user['given_name'],
             'lastname' => $googleUser->user['family_name'],
             'email' => $googleUser->email,
             'password' => 0
         ];

         $result = $this->userService->registerUserViaGoogle($validatedData);

         return Utility::outputResult($result['success'], $result['message'], new UserResource($result['data']), $result['access_token']);

     }

    public function forgetPassword(Request $request)
    {

        try {
            $rules = [
                'email' => ['required', 'string'],
            ];

            $validatedData = $request->validate($rules);

            $token = Utility::token();
            $email = $validatedData['email'];

            #  Find the user by the provided token
            $user = User::where('otp', $token)->first();
            $user = User::where('email', $email)->where('password', $token)->first();

            #  If the user doesn't exist or the token is invalid
            if (!$user) {
                return Utility::outputData(false, "Invalid OTP", [], 422);
            }

            # Retrieve existing access token if available
            $alreadyExist = $user->getCurrentToken();
            if ($alreadyExist) {
                #  If token exists, use it
                $accessToken = $alreadyExist->token;
            } else {
                #  If token doesn't exist, generate a new one
                $accessToken = $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;
            }

            #  Mark the email as verified by setting the current timestamp
            $user->email_verified_at = now();
            $user->save();

            Auth::login($user);

            return Utility::outputData(true, 'Account has been activated', [
                'user' => new UserResource($user),
                'access_token' => $accessToken,
            ], 200);
        } catch (ValidationException $e) {
            return Utility::outputData(false, 'Validation failed', [], 422);
        }
    }
}
