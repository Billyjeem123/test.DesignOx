<?php
namespace App\Services;
use App\Helpers\Utility;
use App\Mail\adminJobNotify;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\NullableType;

class JobService
{
    public function processClientJob(array $data): \Illuminate\Http\JsonResponse
    {
        try {
            #   Process client jobs postings..

            $saveClientJob = Job::create([
                'client_id' => $data['client_id'],
                'project_desc' => $data['project_desc'],
                'budget' => $data['budget'],
                'duration' => $data['duration'],
                'experience_level' => $data['experience_level'],
                'numbers_of_proposals' => $data['numbers_of_proposals'],
                'project_link_attachment' => $data['project_link_attachment']

            ]);
            $newlyCreatedJobId = $saveClientJob->id;

            # Save project types (job types) using attach()
            if (isset($data['project_type'])) {
                $projectTypes = $data['project_type'];
                $saveClientJob->job_type()->attach($projectTypes, ['job_post_id' => $newlyCreatedJobId]);
            }



            $this->saveJobPostingKeyWords($newlyCreatedJobId, $data['keywords']);
            $this->saveJobPostingTools($newlyCreatedJobId, $data['tools_used']);

            Mail::to(config('services.app_config.app_mail'))->send(new adminJobNotify());

            return Utility::outputData(true, "Job posted", [], 201);

        } catch (\Exception $e) {
            #   Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing job posting.' . $e->getMessage(), [], 500);
        }
    }


    public function saveJobPostingKeyWords(int $jobPostingId, array $keywords): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($keywords as $keyword) {
            $data[] = [
                'job_post_id' => $jobPostingId,
                'keywords' => $keyword,
            ];
        }

        // Insert data into the table
        DB::table('job_keywords')->insert($data);
    }

    public function getJobPostingKeyWords($jobPostingId): \Illuminate\Support\Collection
    {
        return DB::table('job_keywords')
            ->select(['keywords']) // Specify the columns you want to include
            ->where('job_post_id', $jobPostingId)
            ->get();
    }


    public function saveJobPostingTools(int $jobPostingId, array $tools): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($tools as $tool) {
            $data[] = [
                'job_post_id' => $jobPostingId,
                'tools' => $tool,
            ];
        }

        // Insert data into the table
        DB::table('job_tools')->insert($data);
    }


    public function getJobPostingTools($jobPostingId): \Illuminate\Support\Collection
    {
        return DB::table('job_tools')
            ->select(['tools']) // Specify the columns you want to include
            ->where('job_post_id', $jobPostingId)
            ->get();
    }



    public function fetchJobsByClient($clientId, $onGoing)
    {
        $query = Job::where('client_id', $clientId);

        if ($onGoing == 1) {
            $query->where('on_going', 1);
        } elseif ($onGoing == 2) {
            $query->where('on_going', 2);
        }

        # Paginate the results
        return $query->paginate(10);

    }

    public function fetchJobById($usertoken, $jobId)
    {
        $job = Job::where('client_id', $usertoken)
            ->where('id', $jobId)
            ->first();

        if(!$job){
            return Utility::outputData(false, "Job not found", [], 404);
        }


        # Load additional data
        $tools = $this->getJobPostingTools($job->id);
        $keywords = $this->getJobPostingKeyWords($job->id);
        $projectType   =  $job->job_type()->pluck('job_type.project_type')->toArray();

        # Return both the job data and additional data as an array
        $jobById =  [
            'job' => [
                'job_post_id' => $job->id,
                'usertoken' => $job->client_id ?? 0,
                'project_desc' => $job->project_desc ?? null,
                'project_budget' => $job->budget ?? 0,
                'project_duration' => $job->duration ?? 0,
                'experience_level' => $job->experience_level ?? 0,
                'numbers_of_proposals' => $job->numbers_of_proposals ?? 0,
                'project_link_attachment' => $job->project_link_attachment ?? 0,
                'on_going' => $job->on_going === 0 ? 'pending' : ($job->on_going === 1 ? 'on_going' : 'completed'),
            ],
            'tools' => $tools,
            'keywords' => $keywords,
            'project_type' => $projectType,
        ];

        return  Utility::outputData(true, "Job fetched successfully", ($jobById), 200);
    }

    public function transformJobData($jobs)
    {
        $jobs->getCollection()->transform(function ($job) {
            $job->tools = $this->getJobPostingTools($job->id);
            $job->keywords = $this->getJobPostingKeyWords($job->id);
            return $job;
        });

        return $jobs->getCollection();
    }

    public function updateJob(int $jobId, array $data, array $keywords, array $tools, array $project_tools): \Illuminate\Http\JsonResponse
    {
        try {
            # Update job details
            $affectedRows = Job::where('id', $jobId)->update($data);

            # Check if any rows were affected
            if ($affectedRows === 0) {
                return Utility::outputData(false, "Job not found or no changes were made", [], 404);
            }

            # Retrieve the updated job instance
            $job = Job::findOrFail($jobId);

            # Update keywords and tools
            $this->updateJobKeywords($jobId, $keywords);
            $this->updateJobTools($jobId, $tools);

            # Sync project types
            $job->job_type()->sync($project_tools);

            return Utility::outputData(true, "Job updated successfully", [], 200);

        } catch (\Exception $e) {
            throw new \Exception('An error occurred while updating job and keywords: ' . $e->getMessage());
        }
    }


    private function updateJobKeywords(int $jobId, array $keywords): void
    {
        # Get the existing keywords for the job
        $existingKeywords = DB::table('job_keywords')->where('job_post_id', $jobId)->pluck('keywords')->toArray();

        # Find keywords to delete
        $keywordsToDelete = array_diff($existingKeywords, $keywords);

        # Find keywords to insert
        $keywordsToInsert = array_diff($keywords, $existingKeywords);

        # Delete keywords that are no longer present
        if (!empty($keywordsToDelete)) {
            DB::table('job_keywords')->where('job_post_id', $jobId)->whereIn('keywords', $keywordsToDelete)->delete();
        }

        # Insert new keywords
        if (!empty($keywordsToInsert)) {
            $data = [];
            foreach ($keywordsToInsert as $keyword) {
                $data[] = [
                    'job_post_id' => $jobId,
                    'keywords' => $keyword,
                ];
            }
            DB::table('job_keywords')->insert($data);
        }
    }


    private function updateJobTools(int $jobId, array $tools):void
    {
        # Get the existing tools for the job
        $existingTools = DB::table('job_tools')->where('job_post_id', $jobId)->pluck('tools')->toArray();

        # Find tools to delete
        $toolsToDelete = array_diff($existingTools, $tools);

        # Find tools to insert
        $toolsToInsert = array_diff($tools, $existingTools);

        # Delete tools that are no longer present
        if (!empty($toolsToDelete)) {
            DB::table('job_tools')->where('job_post_id', $jobId)->whereIn('tools', $toolsToDelete)->delete();
        }

        # Insert new tools
        if (!empty($toolsToInsert)) {
            $data = [];
            foreach ($toolsToInsert as $tool) {
                $data[] = [
                    'job_post_id' => $jobId,
                    'tools' => $tool,
                ];
            }
            DB::table('job_tools')->insert($data);
        }
    }

    public function deleteJob(int $jobId): \Illuminate\Http\JsonResponse
    {
        try {
            # Delete job details
            $affectedRows = Job::where('id', $jobId)->delete();

            if ($affectedRows === 0) {
                # No job was found with the given ID
                return Utility::outputData(false, "Job not found", [], 404);
            }

            return Utility::outputData(true, "Job deleted successfully", [], 200);

        } catch (\Exception $e) {
            throw new \Exception('An error occurred while deleting job and related data: ' . $e->getMessage());
        }
    }


    public function fetchAllJobs($filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Job::query()->orderByDesc('created_at'); # Newest first by default

        if (isset($filters['on_going'])) {
            $query->where('on_going', $filters['on_going']);
        }

        if (isset($filters['experience_level'])) {
            $query->where('experience_level', $filters['experience_level']);
        }

        if (isset($filters['numbers_of_proposals'])) {
            $this->applyProposalFiler($query, $filters['numbers_of_proposals']);
        }

        if (isset($filters['project_budget'])) {
            $this->applyPriceFilter($query, $filters['project_budget']);
        }

        if (isset($filters['time_posted'])) {
            $this->applyTimePostedFilter($query, $filters['time_posted']);
        }

        if (isset($filters['project_type'])) {
            $this->applyProjectTypeFilter($query, $filters['project_type']);
        }

        # Paginate the results
        return $query->paginate(10);
    }

    private function applyProjectTypeFilter($query, $projectTypeId): void
    {
        $query->whereHas('job_type', function ($subQuery) use ($projectTypeId) {
            $subQuery->where('job_type_id', $projectTypeId);
        });
    }




    private function applyProposalFiler($query, $proposals): void
      {
          if (isset($proposals)) {
              switch ($proposals) {
                  case '1-10':
                      $query->whereBetween('numbers_of_proposals', [1, 10]);
                      break;
                  case '11-20':
                      $query->whereBetween('numbers_of_proposals', [11, 20]);
                      break;
                  case '21-30':
                      $query->whereBetween('numbers_of_proposals', [21, 30]);
                      break;
                  // If 'all' or unknown value is provided, don't apply proposal  filter
                  default:
                      break;
              }
          }

      }

    private function applyPriceFilter($query, $priceRange): void
    {
        if (isset($priceRange)) {
            switch ($priceRange) {
                case 'below_100':
                    $query->where('budget', '<', 100);
                    break;
                case '100_500':
                    $query->whereBetween('budget', [100, 500]);
                    break;
                case 'above_500':
                    $query->where('budget', '>', 500);
                    break;
                // If 'all' or unknown value is provided, don't apply price filter
                default:
                    break;
            }
        }
    }



    # Helper method to apply time posted filter
    private function applyTimePostedFilter($query, $timePosted): void
    {
        switch ($timePosted) {
            case '7':
                $query->whereDate('created_at', '>=', now()->subDays(7));
                break;
            case '14':
                $query->whereDate('created_at', '>=', now()->subDays(14));
                break;
            case '24':
                $query->where('created_at', '>=', now()->subHours(24));
                break;
            case '30':
                $query->whereDate('created_at', '>=', now()->subDays(30));
                break;
            case '90':
                $query->whereDate('created_at', '>=', now()->subMonths(3));
                break;
            // If 'all' or unknown value is provided, don't apply time filter
            default:
                break;
        }

    }

    public function saveJob(int $jobPostingId, int $usertoken): \Illuminate\Http\JsonResponse
    {
        # Check if the user token already exists in the save_jobs table
        $existingRecord = DB::table('save_jobs')
            ->where('job_post_id', $jobPostingId)
            ->where('user_id', $usertoken)
            ->exists();

        if ($existingRecord) {
            return Utility::outputData(false, "Job already saved for this user", [], 400);
        }

        # Prepare data for insertion
        $data =  [
            'job_post_id' => $jobPostingId,
            'user_id' => $usertoken,
        ];

        # Insert data into the table
        if (!empty($data)) {
            DB::table('save_jobs')->insert($data);
        }

        return Utility::outputData(true, "Job saved successfully", [], 201);
    }






}