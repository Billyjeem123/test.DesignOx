<?php

namespace App\Services;



use App\Helpers\Utility;
use App\Mail\adminDesignNotify;
use App\Models\Design;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DesignService
{


    public function processDesignUpload(array $data): \Illuminate\Http\JsonResponse
    {
        try {
            #   Process client jobs postings..

            $saveTalentDesign = Design::create([
                'talent_id' => $data['talent_id'],
                'project_desc' => $data['project_desc'],
                'project_title' => $data['project_title'],
                'project_price' => $data['project_price'],
                'attachment' => $data['attachment'],
                'downloadable_file' => $data['downloadable_file'],

            ]);
            $newlyCreatedDesignId = $saveTalentDesign->id;

            # Save project types (job types) using attach()
            if (isset($data['project_type'])) {
                $projectTypes = $data['project_type'];
                $saveTalentDesign->design_type()->attach($projectTypes, ['job_design_id' => $newlyCreatedDesignId]);
            }

            $this->saveDesignPostingKeyWords($newlyCreatedDesignId, $data['keywords']);
            $this->saveDesignPostingTools($newlyCreatedDesignId, $data['tools_used']);

            Mail::to(config('services.app_config.app_mail'))->send(new adminJobNotify());

            return Utility::outputData(true, "Design posted", [], 201);

        } catch (\Exception $e) {
            #   Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing job posting.' . $e->getMessage(), Utility::getExceptionDetails($e), 500);
        }
    }

    private function saveDesignPostingKeyWords($newlyCreatedDesignId, mixed $keywords): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($keywords as $keyword) {
            $data[] = [
                'job_design_id' => $newlyCreatedDesignId,
                'keywords' => $keyword,
            ];
        }

        // Insert data into the table
        DB::table('job_designs_keyword')->insert($data);
    }

    private function saveDesignPostingTools($newlyCreatedDesignId, mixed $tools_used): void
    {

        // Prepare data for insertion
        $data = [];
        foreach ($tools_used as $tool) {
            $data[] = [
                'job_design_id' => $newlyCreatedDesignId,
                'tools' => $tool,
            ];
        }

        // Insert data into the table
        DB::table('job_design_tools')->insert($data);
    }

}