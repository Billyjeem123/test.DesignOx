<?php

namespace App\Services;
use App\Helpers\Utility;
use App\Http\Resources\UserResource;
use App\Mail\forgetPassword;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Nette\Schema\ValidationException;

class UserService
{


    public function registerUser(array $userData)
    {
        # Generate token for OTP
        $token = Utility::token();

        unset($userData['confirm_password']);

        # Create user record with additional data
        return User::create(array_merge($userData, ['otp' => $token, 'account_type' => 'local']));
    }

    public function registerUserViaGoogle(array $userData): array
    {
        # Check if the user already exists
        $existingUser = User::where('email', $userData['email'])->first();

        if ($existingUser) {
            # Check if the existing user's account is local
            if ($existingUser->account_type === 'local') {
                # User already has an account, cannot register using Gmail
                return ['success' => false, 'message' => 'A user account with this email already exists. Please log in using your existing account or use a different email to register.', 'status_code' => 409];
            }

            # Update existing user's account to link with Google authentication
            $existingUser->google_id =  $userData['google_id'];
            $existingUser->account_type = 'google';
            $existingUser->save();
            $accessToken = $existingUser->createToken('API Token of ' . $existingUser['email'], ['read'])->plainTextToken;
            return ['success' => false, 'message' => 'User logged in successfully.', 'data' => new UserResource($existingUser), 'access_token' => $accessToken, 'status_code' => 200];
        }

        # Create new user record with Google authentication
        $user = User::create(array_merge($userData, ['otp' => 0, 'account_type' => 'google']));

        Auth::loginUsingId($user->id);

        return ['success' => true, 'message' => 'User registered successfully.', 'data' => new UserResource($user), 'status_code' => 200];
    }



    public function login(array $credentials): array
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        $user = User::where('email', $email)->first();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.', 'status' => 404];
        }

        if ($user->account_type !== 'local') {
            return ['success' => false, 'message' => 'Please log in using your Google account.', 'status' => 401];
        }

        if (!Hash::check($password, $user->password)) {
            return ['success' => false, 'message' => 'Incorrect email or password', 'status' => 422];
        }

        if (!$user->email_verified_at) {
            return ['success' => false, 'message' => 'Account not verified', 'status' => 422];
        }

       $isActive =  $this->checkIfUserHasSecurityQuestions($user->id);

        $accessToken = $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;
        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'security_access' => [
                    'access_token' => $accessToken,
                    'security_question' => $isActive
                    ],
            ],
            'status' => 200
        ];
    }

    public function verifyOTP(array $credentials): array
    {

        $otp = $credentials['otp'];
        $email = $credentials['email'];

        #  Find the user by the provided token
        $user = User::where('otp', $otp)->first();

        #  If the user doesn't exist or the token is invalid
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid OTP.', 'status' => 422];
        }

        #  Mark the email as verified by setting the current timestamp
        $user->email_verified_at = now();
        $user->save();

        return [
            'success' => true,
            'message' => 'Verification completed',
            'data' => [
                'user' => new UserResource($user),

            ],
            'status' => 200
        ];

    }

    public function saveUserCountry(array $credentials): array
    {

        $user = User::find($credentials['usertoken']);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found', 'status' => 422];
        }

        $this->updateUserCountry($user, $credentials['country']);
        $this->attachUserRole($user, $credentials['role']);

        # Authenticate the user
        Auth::loginUsingId($user->id);

        $accessToken = $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;

        return [
            'success' => true,
            'message' => 'Record saved successfully',
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $accessToken,
            ],
            'status' => 200
        ];


    }


    private function updateUserCountry(User $user, string $country): void
    {
        $user->update(['country' => $country]);
    }

    private function attachUserRole(User $user, string $roleName): void
    {
        $role = Role::where('role_name', $roleName)->first();

        if (!$user->roles()->where('role_name', $roleName)->exists()) {
            $user->roles()->attach($role);
        }
    }


    public function forgetPassword($email): array
    {

        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found', 'status' => 422];
        }

        $token =  Utility::token();
        $hashedPassword = Hash::make($token);

        $user->password = $hashedPassword;
        $user->save();

        Mail::to($user->email)->send(new forgetPassword($user->fullname, $token, config('services.app_config.app_name') ));

        return ['success' => true, 'message' => 'Password sent to mail',  'data' => $user, 'status' => 200];

    }

    public function updatePassword(array $data): \Illuminate\Http\JsonResponse
    {
        try {

            # Retrieve the authenticated user
            $user = Auth::user();

            if($user->account_type == 'google'){
                return Utility::outputData(false, "You cannot update your password because you registered with Google.", [], 200);

            }

            # Verify the old password
            if (!Hash::check($data['old_password'], $user->password)) {
                return Utility::outputData(false, "Old password is incorrect", [], 400);
            }

            # Hash the new password
            $hashedPassword = Hash::make($data['new_password']);

            #  Update the user's password
            $user->password = $hashedPassword;
            $user->save();

            return Utility::outputData(true, "Password updated successfully", [], 200);
        } catch (ValidationException $e) {
            // Handle validation errors
            return Utility::outputData(false, "Validation failed", $e->getMessage(), 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }


    public function processSecurityQuestion(array $data): array
    {
        # Check if the user has already set up a security question
        $existingQuestion = DB::table('tblsecurity_question')
            ->where('user_id', $data['usertoken'])
            ->first();

        if ($existingQuestion) {
            # User already has a security question set up
            return ['success' => false, 'message' => 'You have already set up a security question.', 'status' => 400];
        }

        # Insert the security question into the database
        DB::table('tblsecurity_question')->insert($data);

        return ['success' => true, 'message' => 'Security question set successfully', 'status' => 201];
    }

    private function checkIfUserHasSecurityQuestions(int $userid): bool
    {

        # Check if the user exists in the security questions table
        $securityQuestion = DB::table('tblsecuity_question')
            ->where('user_id', $userid)
            ->where('is_activated', 1)
            ->first();

        # Return true if a security question record is found, false otherwise
        return (bool)$securityQuestion;
    }


    public function manageSecurityQuestion(array $data): array
    {

        # Update the is_activated column for the user in the security questions table
        $updated = DB::table('tblsecuity_question')
            ->where('user_id', $data['usertoken'])
            ->update(['is_activated' => $data['is_activated']]);

        $message = ($data['is_activated'] === 1) ? 'Activated successfully' : 'Deactivated successfully';


        # Return true if the update operation was successful, false otherwise
        return ['success' => true, 'message' => $message, 'status' => 201];
    }



    public function getSecurityQuestions(int $userId, $verify = null): \Illuminate\Http\JsonResponse
    {
        # Check if the user exists in the security questions table
        $securityQuestion = DB::table('tblsecuity_question')
            ->where('user_id', $userId)
            ->where('is_activated', 1)
            ->first();

        # If verification is requested and answer is provided, verify the answer
        if ($verify) {
            if ($securityQuestion && $verify === $securityQuestion->answer) {
                #  Return success response if answer matches
                return Utility::outputData(true, "Verification successful", [], 200);
            } else {
                # Return failure response if answer does not match
                return Utility::outputData(false, "Incorrect security answer", [], 422);
            }
        }

        # Return the security question
        if ($securityQuestion) {
            $data =  [
                'question' => $securityQuestion->question,
                'usertoken' => $securityQuestion->user_id, # Include user ID for reference
            ];
            return Utility::outputData(true, "Question fetched successfully", $data, 200);

        } else {
            # Return error response if no security question is found
            return Utility::outputData(false, "No security question found for the user", [], 404);
        }
    }







}