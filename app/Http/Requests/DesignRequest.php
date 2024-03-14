<?php

namespace App\Http\Requests;

use App\Rules\ArrayValidation;
use Illuminate\Foundation\Http\FormRequest;

class DesignRequest extends FormRequest
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
            case 'uploadDesign':
                return [
                    'project_desc' => ['required', 'string', 'max:255'],
                    'project_title' => ['required', 'string', 'max:255'],
                    'project_type' => ['required', new ArrayValidation()],
                    'tools_used' => ['required', new ArrayValidation()],
                    'keywords' => ['required', new ArrayValidation()],
                    'images' => ['required', 'array', new ArrayValidation()],
                    'colors' => ['required', 'array', new ArrayValidation()],
                    'images.*' => ['required', 'string', 'regex:/^data:image\/(jpeg|png|jpg|gif);base64/', 'max:5242880'], // Adjusted max size to accommodate Base64 encoding overhead
                    'project_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
                    'attachment' => ['required', 'url', 'max:255'],
                    'downloadable_file' => ['required', 'url', 'max:255']
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
}
