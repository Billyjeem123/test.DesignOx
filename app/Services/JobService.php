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
    public function processClientJob(array $data)
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


            $this->saveJobPostingKeyWords($newlyCreatedJobId, $data['keywords']);
            $this->saveJobPostingTools($newlyCreatedJobId, $data['tools_used']);
            $this->saveJobPostingProjectType($newlyCreatedJobId, $data['project_type']);

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
        DB::table('tbljob_keywords')->insert($data);
    }

    public function getJobPostingKeyWords($jobPostingId): \Illuminate\Support\Collection
    {
        return DB::table('tbljob_keywords')
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
        DB::table('tbljob_posting_tools')->insert($data);
    }


    public function getJobPostingTools($jobPostingId): \Illuminate\Support\Collection
    {
        return DB::table('tbljob_posting_tools')
            ->select(['tools']) // Specify the columns you want to include
            ->where('job_post_id', $jobPostingId)
            ->get();
    }




    public function saveJobPostingProjectType(int $jobPostingId, array $project_types): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($project_types as $project_type) {
            $data[] = [
                'job_post_id' => $jobPostingId,
                'project_type' => $project_type,
            ];
        }

        // Insert data into the table
        DB::table('tbljob_posting_projecttype')->insert($data);
    }


    public function getJobPostingProjectType($jobPostingId): \Illuminate\Support\Collection
    {
        return DB::table('tbljob_posting_projecttype')
            ->select(['project_type']) # Specify the columns you want to include
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

        if (!$job) {
            return null; # Or handle the case where the job is not found
        }

        # Load additional data
        $tools = $this->getJobPostingTools($job->id);
        $keywords = $this->getJobPostingKeyWords($job->id);
        $projectType = $this->getJobPostingProjectType($job->id);

        # Return both the job data and additional data as an array
        return [
            'job' => [
                'job_post_id' => $job->id,
                'usertoken' => $job->client_id ?? 0,
                'project_desc' => $job->project_desc ?? null,
                'project_budget' => $job->budget ?? 0,
                'project_duration' => $job->duration ?? 0,
                'experience_level' => $job->experience_level ?? 0,
                'numbers_of_proposals' => $job->numbers_of_proposals ?? 0,
                'project_link_attachment' => $job->project_link_attachment ?? 0,
                'on_going' => $job->on_going === 0 ? 'pending' : ($job->on_going === 1 ? 'approved' : 'finished'),
            ],
            'tools' => $tools,
            'keywords' => $keywords,
            'project_type' => $projectType,
        ];
    }

    public function transformJobData($jobs)
    {
        $jobs->getCollection()->transform(function ($job) {
            $job->tools = $this->getJobPostingTools($job->id);
            $job->keywords = $this->getJobPostingKeyWords($job->id);
            $job->project_type = $this->getJobPostingProjectType($job->id);
            return $job;
        });

        return $jobs->getCollection();
    }

    public function updateJob(int $jobId, array $data, array $keywords, array $tools, array $project_tools)
    {
        try {
            # Update job details
            Job::where('id', $jobId)->update($data);

            # Update keywords
            $this->updateJobKeywords($jobId, $keywords);
             $this->updateJobTools($jobId, $tools);
            $this->updateJobProjectType($jobId, $project_tools);

            return Utility::outputData(true, "Job updated successfully", [], 200);

        } catch (\Exception $e) {
            throw new \Exception('An error occurred while updating job and keywords: ' . $e->getMessage());
        }
    }

    private function updateJobKeywords(int $jobId, array $keywords): void
    {
        # Get the existing keywords for the job
        $existingKeywords = DB::table('tbljob_keywords')->where('job_post_id', $jobId)->pluck('keywords')->toArray();

        # Find keywords to delete
        $keywordsToDelete = array_diff($existingKeywords, $keywords);

        # Find keywords to insert
        $keywordsToInsert = array_diff($keywords, $existingKeywords);

        # Delete keywords that are no longer present
        if (!empty($keywordsToDelete)) {
            DB::table('tbljob_keywords')->where('job_post_id', $jobId)->whereIn('keywords', $keywordsToDelete)->delete();
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
            DB::table('tbljob_keywords')->insert($data);
        }
    }


    private function updateJobTools(int $jobId, array $tools):void
    {
        # Get the existing tools for the job
        $existingTools = DB::table('tbljob_posting_tools')->where('job_post_id', $jobId)->pluck('tools')->toArray();

        # Find tools to delete
        $toolsToDelete = array_diff($existingTools, $tools);

        # Find tools to insert
        $toolsToInsert = array_diff($tools, $existingTools);

        # Delete tools that are no longer present
        if (!empty($toolsToDelete)) {
            DB::table('tbljob_posting_tools')->where('job_post_id', $jobId)->whereIn('tools', $toolsToDelete)->delete();
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
            DB::table('tbljob_posting_tools')->insert($data);
        }
    }



    private function updateJobProjectType(int $jobId, array $project_type): void
    {
        # Get the existing project types for the job
        $existingProjectTypes = DB::table('tbljob_posting_projecttype')->where('job_post_id', $jobId)->pluck('project_type')->toArray();

        # Find project types to delete
        $projectTypesToDelete = array_diff($existingProjectTypes, $project_type);

        # Find project types to insert
        $projectTypesToInsert = array_diff($project_type, $existingProjectTypes);

        # Delete project types that are no longer present
        if (!empty($projectTypesToDelete)) {
            DB::table('tbljob_posting_projecttype')->where('job_post_id', $jobId)->whereIn('project_type', $projectTypesToDelete)->delete();
        }

        # Insert new project types
        if (!empty($projectTypesToInsert)) {
            $data = [];
            foreach ($projectTypesToInsert as $project) {
                $data[] = [
                    'job_post_id' => $jobId,
                    'project_type' => $project,
                ];
            }
            DB::table('tbljob_posting_projecttype')->insert($data);
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

        if (isset($filters['experience'])) {
            $query->where('experience_level', $filters['experience']);
        }

        if (isset($filters['proposals'])) {
            $this->applyProposalFiler($query, $filters['proposals']);
        }

        if (isset($filters['budget'])) {
            $this->applyPriceFilter($query, $filters['budget']);
        }

        if (isset($filters['time_posted'])) {
            $this->applyTimePostedFilter($query, $filters['time_posted']);
        }

        # Paginate the results
        return $query->paginate(10);
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






}