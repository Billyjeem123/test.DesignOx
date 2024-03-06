<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            case 'sendClientProposal':
                return [
                    'client_id' => ['required', 'string', 'max:255'],
                    'job_post_id' => ['required', 'string', 'max:255'],
                    'cover_letter' => ['required', 'integer', 'min:1'],
                    'attachment' => ['required', 'url', 'max:255'],
                    'preferred_date' => ['required', 'string', 'max:255'],

                ];

                break;
            default:
                return [];
        }
    }
}
