<?php

namespace App\Services;



use App\Helpers\Utility;
use App\Mail\adminDesignNotify;
use App\Models\Design;
use App\Models\Images;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

            # Save uploaded images
            if (isset($data['images'])) {
                foreach ($data['images'] as $image) {
                    # Assuming images are base64 encoded strings, decode and save each one
                    $decodedImage = base64_decode($image);

                    $newName = time().'.'.$decodedImage;
                    $imageModel = new Images();
                    $imageModel->job_design_id = $saveTalentDesign->id;
                    # Save image to storage and get the path, adjust this according to your storage setup
                    $imageModel->path = Storage::put('images', $newName);
                    $imageModel->save();
                }
            }

            $this->saveDesignPostingKeyWords($newlyCreatedDesignId, $data['keywords']);
            $this->saveDesignPostingTools($newlyCreatedDesignId, $data['tools_used']);

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

        # Prepare data for insertion
        $data = [];
        foreach ($tools_used as $tool) {
            $data[] = [
                'job_design_id' => $newlyCreatedDesignId,
                'tools' => $tool,
            ];
        }

        # Insert data into the table
        DB::table('job_design_tools')->insert($data);
    }

}