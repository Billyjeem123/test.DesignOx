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
            echo json_encode($data['tools_used']);

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

    public function getJobPostingKeyWords($jobPostingId)
    {
        return DB::table('tbljob_keywords')
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
        DB::table('tbljob_tools')->insert($data);
    }


    public function getJobPostingTools($jobPostingId)
    {
        return DB::table('tbljob_tools')
            ->where('job_post_id', $jobPostingId)
            ->get();
    }








}