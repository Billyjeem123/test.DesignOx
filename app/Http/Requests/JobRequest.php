<?php

namespace App\Http\Requests;

use App\Rules\ArrayValidation;
use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
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
    public function rules()
    {
        switch ($this->route()->getActionMethod()) {
            case 'createJob':
                return [
                    'project_desc' => ['required', 'string', 'max:255'],
                    'project_title' => ['required', 'string', 'max:255'],
                    'project_type' => ['required', new ArrayValidation()],
                    'tools_used' => ['required', new ArrayValidation()],
                    'keywords' => ['required', new ArrayValidation()],
                    'budget' => ['required', 'numeric', 'min:0'],
                    'duration' => ['required', 'string', 'max:255'],
                    'numbers_of_proposals' => ['required', 'integer', 'min:1'],
                    'experience_level' => ['required', 'string', 'max:255'],
                    'project_link_attachment' => ['required', 'url', 'max:255']
                ];

            case 'deleteJobById':
                return [
                    'job_post_id' => 'required'
                ];

            case 'deleteSavedJobs':
                return [
                    'job_post_id' => 'required'
                ];

            case 'saveJob':
                return [
                    'job_post_id' => 'required'
                ];

            case 'updateJobById':
                return [
                    'project_desc' => ['required', 'string', 'max:255'],
                    'project_title' => ['required', 'string', 'max:255'],
                    'project_type' => ['required', new ArrayValidation()],
                    'tools_used' => ['required', new ArrayValidation()],
                    'keywords' => ['required', new ArrayValidation()],
                    'budget' => ['required', 'numeric', 'min:0'],
                    'duration' => ['required', 'string', 'max:255'],
                    'numbers_of_proposals' => ['required', 'integer', 'min:1'],
                    'experience_level' => ['required', 'string', 'max:255'],
                    'project_link_attachment' => ['required', 'url', 'max:255']
                ];

                break;
            default:
                return [];
        }
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'budget.numeric' => 'The budget must be a number.',
            'numbers_of_proposals.integer' => 'The number of proposals must be a whole number.',
            'numbers_of_proposals.min' => 'The number of proposals must be at least 1.',
            'project_link_attachment.url' => 'The project link attachment must be a valid URL.',
            // Add more custom error messages as needed
        ];
    }
}
