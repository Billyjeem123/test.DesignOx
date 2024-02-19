<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch ($this->route()->getActionMethod()) {
            case 'register':
                return [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'email' => 'required|email|unique:tblusers',
                    'password' => 'required|string',
                ];
            case 'verifyOTP':
                return [
                    'token' => 'required|string',
                    'email' => 'required|email',
                ];
            case 'login':
                return [
                    'email' => 'required|email',
                    'password' => 'required|string',
                ];
            default:
                return [];
        }
    }



}
