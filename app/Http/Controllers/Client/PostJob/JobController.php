<?php

namespace App\Http\Controllers\Client\PostJob;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Helpers\Utility;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    protected  $jobService;
    public  $tools;
    public  $keywords;

    public $project_type;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService; #  Inject JobService instance
    }

    public function createJob(JobRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $user = auth('api')->user();

            $data = [
                'client_id' => $user->id,
                'project_desc' => $validatedData['project_desc'],
                'project_type' => $validatedData['project_type'],
                'tools_used' => $validatedData['tools_used'],
                'budget' => $validatedData['budget'],
                'duration' => $validatedData['duration'],
                'experience_level' => $validatedData['experience_level'],
                'numbers_of_proposals' => $validatedData['numbers_of_proposals'],
                'project_link_attachment' => $validatedData['project_link_attachment'],
                'keywords' => $validatedData['keywords'],
                'job_status' =>0
            ];

            return $this->jobService->processClientJob($data);

        } catch (ValidationException $e) {
            #  Return validation errors
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            #  Handle any other exceptions that may occur during role creation
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }




    public function getClientJobPosting($on_going=null): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth('api')->user();
            $jobsByClient = $this->jobService->fetchJobsByClient($user->id, $on_going);

            if ($jobsByClient->isEmpty()) {
                return Utility::outputData(false, "No results found", [], 404);
            }

            $responseData = $this->jobService->transformJobData($jobsByClient);

            # Return paginated response
           return  Utility::outputData(true, "Client jobs fetched  successfully", new JobResource($responseData), 200);

        } catch (\PDOException $e) {
            # Handle PDOException
           return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        }
    }


    public function getJobById(int $jobId): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth('api')->user();
            $job = $this->jobService->fetchJobById($user->id, $jobId);

            if (!$job) {
                return Utility::outputData(false, "Job not found", [], 404);
            }


            return  Utility::outputData(true, "Job fetched successfully", ($job), 200);
        } catch (\PDOException $e) {
            return  Utility::outputData(false, "Unable to process request: " . $e->getMessage(), [], 400);
        }
    }



    public function updateJobById(JobRequest $request, $jobId)
    {
        try {
            $validatedData = $request->validated();
            $user = auth('api')->user();

            $data = [
                'client_id' => $user->id,
                'project_desc' => $validatedData['project_desc'],
                'budget' => $validatedData['budget'],
                'duration' => $validatedData['duration'],
                'experience_level' => $validatedData['experience_level'],
                'numbers_of_proposals' => $validatedData['numbers_of_proposals'],
                'project_link_attachment' => $validatedData['project_link_attachment'],
            ];

            return $this->jobService->updateJob($jobId, $data, $validatedData['keywords'], $validatedData['tools_used'], $validatedData['project_type']);


        } catch (ValidationException $e) {
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }



    public function deleteJobById(JobRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'job_post_id' => $validatedData['job_post_id'],
            ];

            return $this->jobService->deleteJob($data['job_post_id']);


        } catch (ValidationException $e) {
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }







}


