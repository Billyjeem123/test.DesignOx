<?php

namespace App\Services;



use App\Helpers\Utility;
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
            #   Process client jobs postings

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

            $this->saveDesignImages($data['images'], $newlyCreatedDesignId);
            $this->saveDesignPostingKeyWords($newlyCreatedDesignId, $data['keywords']);
            $this->saveDesignPostingTools($newlyCreatedDesignId, $data['tools_used']);
            $this->saveJobColor($newlyCreatedDesignId, $data['colors']);

            return Utility::outputData(true, "Design posted", [], 201);

        } catch (\Exception $e) {
            #   Handle exceptions
            return Utility::outputData(false, 'An error occurred while processing job posting', Utility::getExceptionDetails($e), 500);
        }
    }


    private function saveDesignImages($images, $designId): void
    {
        // Save uploaded images
        if (isset($images)) {
            foreach ($images as $image) {
                # Assuming images are base64 encoded strings, decode and save each one
                $decodedImage = base64_decode($image);

                $extension = $this->getImageExtension($decodedImage);

                # Generate a unique filename for the image
                $newName = time() . '_' . uniqid() . '.' . $extension;

                # Save the image to the storage directory
                Storage::put('images/' . $newName, $decodedImage);

                #  Create a new Image model instance
                $imageModel = new Images();
                $imageModel->images = $newName; #  Save the filename
                $imageModel->job_design_id = $designId;

                // Save the image model
                $imageModel->save();
            }
        }
    }



    private function getImageExtension($decodedImage): string
    {
        $mimeType = Storage::mimeType('data:image/png;base64,' . base64_encode($decodedImage));

        // Determine the file extension based on MIME type
        switch ($mimeType) {
            case 'image/jpeg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            // Add more cases as needed for other image formats
            default:
                return 'jpg'; // Default to jpg if extension not recognized
        }
        }

        private  function saveJobColor(int $jobDesignId, array $jobColorIds): void
    {
        // Prepare data for insertion
        $data = [];
        foreach ($jobColorIds as $jobColorId) {
            $data[] = [
                'job_design_id' => $jobDesignId,
                'color_id' => $jobColorId,
            ];
        }

        // Insert data into the pivot table
        DB::table('job_design_job_colors')->insert($data);
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