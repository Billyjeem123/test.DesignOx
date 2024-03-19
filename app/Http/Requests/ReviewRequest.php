<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        switch ($this->route()->getActionMethod()) {
            case 'Makereview':
                return [
                    'job_design_id' => ['required', 'integer', 'max:255'],
                    'review' => ['required', 'string'],
                    'ratings' => ['required', 'integer', 'max:255']

                ];

                break;
            default:
                return [];
        }
    }
}
