<?php

namespace App\Http\Controllers\Client\User;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Mail\WelcomeEmail;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
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


    public function verifyOTP(UserRequest $request)
    {

        try {
            $validatedData = $request->validated();

            $token = $validatedData['token'];
            $email = $validatedData['email'];

            #  Find the user by the provided token
            $user = User::where('otp', $token)->first();

            #  If the user doesn't exist or the token is invalid
            if (!$user) {
                return Utility::outputData(false, "Invalid OTP", [], 422);
            }

            #  Check if the provided email matches the user's email
            if ($email !== $user->email) {
                return Utility::outputData(false, "Email doesn't match", [], 422);
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

    public function login(UserRequest $request)
    {
        try {
            # Validate the request data
            $validatedData = $request->validated();

            # Retrieve email and password from validated data
            $email = $validatedData['email'];
            $password = $validatedData['password'];

            # Find the user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return Utility::outputData(false, 'User not found.', [], 404);
            }

            if ($user->account_type !== 'local') {
                return Utility::outputData(false, 'Please log in using your Google account.', [], 401);
            }

            # If user not found or password is incorrect, return error
            if (!$user || !Hash::check($password, $user->password)) {
                return Utility::outputData(false, 'Incorrect email or password', [], 422);
            }

            #  Check if the account is not verified
            if (!$user->email_verified_at) {
                return Utility::outputData(false, 'Account not verified', [], 422);
            }

            # Retrieve existing access token if available
            $alreadyExist = $user->getCurrentToken();
            if ($alreadyExist) {
                #  If token exists, use it
                $accessToken = $alreadyExist->bearerToken;
            } else {
                #  If token doesn't exist, generate a new one
                $accessToken = $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;
            }

            # Attempt to login the user
            Auth::login($user);

            # Return success response with user data and access token
            return Utility::outputData(true, 'Login successful', [
                'user' => new UserResource($user),
                'access_token' => $accessToken,
            ], 200);
        } catch (ValidationException $e) {
            # Handle validation errors
            return Utility::outputData(false, $e->getMessage(), [], 422);
        } catch (\Exception $e) {
            # Handle other unexpected errors
            return Utility::outputData(false, $e->getMessage(), [], 500);
        }
    }

//     public function logout()
// {
//     try {
//         $logout = auth()->user()->tokens()->delete();
//         if ($logout) {
//             return Utility::outputData(true, "Logged out successfully", [], 200);
//         }
//     } catch (\Exception $e) {
//         return Utility::outputData(false, $e->getMessage(), [], 500);
//     }
// }

    public function redirectToGoogle()
    {

        return Socialite::driver('google')->redirect();
    }

    // public function googleCallBack(){

    //     $googleUser = Socialite::driver('google')->stateless()->user();
    //     $userFullName= $googleUser['family_name'];
    //     $userEmail = $googleUser['email'];

    //     echo json_encode($googleUser);
    // }

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
