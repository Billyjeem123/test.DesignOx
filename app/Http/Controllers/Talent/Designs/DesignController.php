<?php

namespace App\Http\Controllers\Talent\Designs;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\DesignRequest;
use App\Models\Design;
use App\Models\Job;
use App\Services\DesignService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DesignController extends Controller
{
     protected DesignService $designService;

    public function __construct(DesignService $designService)
    {
        $this->designService = $designService; #  Inject JobService instance

    }
    public function uploadDesign(DesignRequest $request): \Illuminate\Http\JsonResponse
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
                 'project_type' => $validatedData['project_type'],
                'keywords' => $validatedData['keywords']
            ];

            return $this->designService->processDesignUpload($data);

        } catch (ValidationException $e) {
            #  Return validation errors
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            #  Handle any other exceptions that may occur during role creation
            return Utility::outputData(false, "An error occurred", Utility::getExceptionDetails($e), 500);
        }
    }


    public function getAllDesigns(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $filter = $request->all();

            $talentDesigns = $this->designService->fetchAllDesigns($filter);

            if ($talentDesigns->isEmpty()) {
                return Utility::outputData(false, "No results found", [], 404);
            }

            # Return paginated response
            return  Utility::outputData(true, "All jobs fetched  successfully", (new JobResource($jobsByClient))->toArrayWithMinimalData(), 200);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }
}
