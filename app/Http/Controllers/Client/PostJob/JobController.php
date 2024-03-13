<?php

namespace App\Http\Controllers\Client\PostJob;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Helpers\Utility;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\User;
use App\Policies\JobPolicy;
use App\Services\JobService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class  JobController extends Controller
{
    protected  $jobService;
    public  $tools;
    public  $keywords;

    public  int  $id;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService; #  Inject JobService instance

    }

    public function createJob(JobRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $user = auth('api')->user();

            $this->authorize('create', [Job::class]);

            $data = [
                'client_id' => $user->id,
                'project_desc' => $validatedData['project_desc'],
                'project_title' => $validatedData['project_title'],
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
            # Authorize access for each job
            foreach ($jobsByClient as $job) {
                $this->authorize('view',  $job);
            }

            if ($jobsByClient->isEmpty()) {
                return Utility::outputData(false, "No results found", [], 404);
            }

            $responseData = $this->jobService->transformJobData($jobsByClient);

            # Return paginated response
           return  Utility::outputData(true, "Client jobs fetched  successfully", new JobResource($responseData), 200);

        } catch (\PDOException $e) {
            # Handle PDOException
           return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }


    public function getJobById(int $jobId): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth('api')->user();
            return $this->jobService->fetchJobById($user->id, $jobId);

        } catch (\PDOException $e) {
            return  Utility::outputData(false, "Unable to process request: " . $e->getMessage(), [], 400);
        }
    }



    public function updateJobById(JobRequest $request, $jobId): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = auth('api')->user();
            $data = [
                'client_id' =>$user->id,
                'project_desc' => $validatedData['project_desc'],
                'project_title' => $validatedData['project_title'],
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



    public function deleteJobById(JobRequest $request , Job $job): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'job_post_id' => $validatedData['job_post_id'],
            ];

            $this->authorize('delete', Job::findOrFail($data['job_post_id']));

            return $this->jobService->deleteJob($data['job_post_id']);


        } catch (ValidationException $e) {
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }



    public function getAllJobPosting(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $filter = $request->all();

            $jobsByClient = $this->jobService->fetchAllJobs($filter);

            if ($jobsByClient->isEmpty()) {
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

    public function saveJob(JobRequest $requests): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $requests->validated();
            $auth = Auth::user();

            return $this->jobService->saveJob($validatedData['job_post_id'], $auth->id);

        } catch (\PDOException $e) {
            # Handle PDOException
            return  Utility::outputData(false, "Unable to process request". $e->getMessage(), [], 400);
        } catch (AuthorizationException $e) {
            return  Utility::outputData(false, "Unauthorized access ",  $e->getMessage(), 404);
        }
    }


    public function viewRelatedJobs($clickedJobId): \Illuminate\Http\JsonResponse
    {
        try {
            $clickedJob = Job::find($clickedJobId);

            $relatedJobs = $this->jobService->viewRelatedJobs($clickedJob);

            return Utility::outputData(true, "Related jobs fetched successfully", new JobResource($relatedJobs), 200);
        } catch (\PDOException $e) {
            // Handle PDOException
            return Utility::outputData(false, "Unable to process request: " . $e->getMessage(), [], 400);
        } catch (\Exception $e) {
            // Handle other exceptions
            return Utility::outputData(false, "An error occurred: " . $e->getMessage(), [], 500);
        }
    }

    public function getSpecificJobById(int $jobId): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth('api')->user();
            return $this->jobService->getSpecificJobById($jobId);

        } catch (\PDOException $e) {
            return  Utility::outputData(false, "Unable to process request: " . $e->getMessage(), [], 400);
        }
    }


    public function getSavedJobs(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth('api')->user(); # Retrieve the authenticated user

            # Check if the user is authenticated
            if (!$user) {
                return Utility::outputData(false, "Unauthorized", [], 401);
            }

            # Fetch the saved jobs of the authenticated user
            return $this->jobService->getSavedJobs($user);

        } catch (\PDOException $e) {
            return  Utility::outputData(false, "Unable to process request: " . $e->getMessage(), [], 400);
        }
    }


    public function deleteSavedJobs(JobRequest $request , Job $job): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $data = [
                'job_post_id' => $validatedData['job_post_id'],
            ];

            $this->authorize('delete', Job::findOrFail($data['job_post_id']));

            return $this->jobService->deleteSavedJob($data['job_post_id']);


        } catch (ValidationException $e) {
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        } catch (\Exception $e) {
            return Utility::outputData(false, "An error occurred", $e->getMessage(), 500);
        }
    }










}


