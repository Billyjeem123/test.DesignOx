<?php

namespace App\Services;
use App\Helpers\Utility;
use App\Models\User;

class UserService {


    public function registerUser(array $userData)
    {
        # Generate token for OTP
        $token = Utility::token();

        # Create user record with additional data
        return User::create(array_merge($userData, ['otp' => $token, 'account_type' => 'local']));
    }



}