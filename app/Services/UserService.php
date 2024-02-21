<?php

namespace App\Services;
use App\Helpers\Utility;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService {


    public function registerUser(array $userData)
    {
        # Generate token for OTP
        $token = Utility::token();

        # Create user record with additional data
        return User::create(array_merge($userData, ['otp' => $token, 'account_type' => 'local']));
    }

    public function registerUserViaGoogle(array $userData): array
    {
        # Check if the user already exists
        $existingUser = User::where('email', $userData['email'])->first();
        if ($existingUser) {
            return ['success' => false, 'message' => 'User already exists.', 'data' => new UserResource($existingUser), 'access_token' => null, 'status_code' => 200];
        }

        # Create user record with additional data
        $user = User::create(array_merge($userData, ['otp' => 0, 'account_type' => 'google']));

        $accessToken = $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;

        Auth::login($user);

        return ['success' => true, 'message' => 'User registered successfully.', 'data' => new UserResource($user), 'access_token' => $accessToken, 'status_code' => 200];
    }



    public function login(array $credentials)
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

        $accessToken = $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;
        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $accessToken,
            ],
            'status' => 200
        ];
    }

    public function verifyOTP(array $credentials){

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

        $accessToken = $this->generateAccessToken($user);

        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Verification completed',
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $accessToken,
            ],
            'status' => 200
        ];

    }

    public function saveUserCountry(array $credentials): array
    {

        $user = User::find($credentials['usertoken']);
        if(!$user){
            return ['success' => false, 'message' => 'User not found', 'status' => 422];
        }

        $this->updateUserCountry($user, $credentials['country']);
        $this->attachUserRole($user, $credentials['role']);

        return [
            'success' => true,
            'message' => 'Record saved successfully',
            'data' => [
                'user' => new UserResource($user)
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

    public function generateAccessToken(User $user)
    {
        $alreadyExist = $user->getCurrentToken();
        if ($alreadyExist) {
            return $alreadyExist->bearerToken;
        } else {
            return $user->createToken('API Token of ' . $user->email, ['read'])->plainTextToken;
        }
    }



}