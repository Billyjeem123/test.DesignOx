<?php

namespace App\Http\Controllers\Talent\Designs;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\DesignRequest;
use App\Models\Design;
use App\Models\Job;
use App\Services\DesignService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DesignController extends Controller
{
     protected DesignService $designService;

    public function __construct(DesignService $designService)
    {
        $this->designService = $designService; #  Inject JobService instance

    }
    public function createJob(DesignRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $user = auth('api')->user();

            $this->authorize('create', [Design::class]);

            $data = [
                'talent_id' => $user->id,
                'project_desc' => $validatedData['project_desc'],
                'project_title' => $validatedData['project_title'],
                'tools_used' => $validatedData['tools_used'],
                'project_price' => $validatedData['project_price'],
                'attachment' => $validatedData['attachment'],
                'downloadable_file' => $validatedData['downloadable_file'],
                'images' => $validatedData['images'],
                'colors' => $validatedData['colors'],
                 'project_type' => $validatedData['project_type']
            ];

            return $this->designService->processDesignUpload($data);

        } catch (ValidationException $e) {
            #  Return validation errors
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            #  Handle any other exceptions that may occur during role creation
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }
}
