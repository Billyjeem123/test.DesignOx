<?php

namespace App\Http\Controllers\Talent\Designs;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Http\Requests\DesignRequest;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Services\DesignService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return  Utility::outputData(true, "All designs fetched successfully", (new DesignResource($talentDesigns))->toArrayWithMinimalData(), 200);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }


    public function saveDesign(DesignRequest $requests): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $requests->validated();
            $auth = Auth::user();

            return $this->designService->saveDesign($validatedData['job_design_id'], $auth->id);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }



    public function getSavedDesigns(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth('api')->user(); # Retrieve the authenticated user

            # Check if the user is authenticated
            if (!$user) {
                return Utility::outputData(false, "Unauthorized", [], 401);
            }

            # Fetch the saved designs  of the authenticated user
            return $this->designService->getSavedDesigns($user);

        } catch (\PDOException $e) {
            return  Utility::outputData(false, "Unable to process request: " . $e->getMessage(), [], 400);
        }
    }


    public function likeDesign(DesignRequest $requests): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $requests->validated();
            $auth = Auth::user();

            return $this->designService->LikeDesign($validatedData['job_design_id'], $auth->id);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }


    public function getTalentDesigns(): \Illuminate\Http\JsonResponse
    {
        try {
            $auth = Auth::user();

            $designByTalent =  $this->designService->getTalentDesigns($auth->id);
            if ($designByTalent->isEmpty()) {
                return Utility::outputData(false, "No results found", [], 404);
            }

            $responseData = $this->designService->transformDesignData($designByTalent);

            # Return paginated response
            return  Utility::outputData(true, "Talent Designs fetched  successfully", (new DesignResource($responseData))->toArrayWithMinimalData(), 200);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }


    public function getDesignsById(int $designId): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->designService->fetchDesignById($designId);

        } catch (\PDOException $e) {
            return  Utility::outputData(false, "Unable to process request", Utility::getExceptionDetails($e), 400);
        }
    }




}
