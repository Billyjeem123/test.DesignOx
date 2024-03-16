<?php

namespace App\Services;



use App\Events\LikeDesign;
use App\Helpers\Utility;
use App\Http\Resources\DesignResource;
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
                list($type, $imageData) = explode(';', $image);
                list(, $imageData) = explode(',', $imageData);

                $decodedImage = base64_decode($imageData);

                $extension = $this->getImageExtension($decodedImage);

                # Generate a unique filename for the image
                $newName = time() . '_' . uniqid() . '.' . $extension;

                # Save the image to the storage directory
                Storage::put("images/{$newName}", $decodedImage);

                # Create a new Image model instance
                $imageModel = new Images();
                $imageModel->images = $newName; # Save the filename
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


    public function fetchAllDesigns($filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Design::query()->orderByDesc('created_at'); # Newest first by default

        $user = auth()->user();

        $query->with(['likedByUsers' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }]);

        if (isset($filters['price_range'])) {
            $this->applyPriceFilter($query, $filters['price_range']);
        }

        if (isset($filters['time_posted'])) {
            $this->applyTimePostedFilter($query, $filters['time_posted']);
        }

        # Paginate the results
        return $query->paginate(10);
    }


    private function applyPriceFilter($query, $priceRange): void
    {
        if (isset($priceRange)) {
            switch ($priceRange) {
                case 'below_100':
                    $query->where('project_price', '<', 100);
                    break;
                case '100_500':
                    $query->whereBetween('project_price', [100, 500]);
                    break;
                case 'above_500':
                    $query->where('project_price', '>', 500);
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


    public function saveDesign(int $jobDesignId, int $usertoken): \Illuminate\Http\JsonResponse
    {
        $recordExists  = Design::find($jobDesignId);
        if (!$recordExists) {
            return Utility::outputData(false, "Record could not be found", [], 400);
        }


        # Check if the user token already exists in the save_design table
        $existingRecord = DB::table('save_designs')
            ->where('job_design_id', $jobDesignId)
            ->where('user_id', $usertoken)
            ->exists();

        if ($existingRecord) {
            return Utility::outputData(false, "Design already saved by this user", [], 400);
        }

        # Prepare data for insertion
        $data =  [
            'job_design_id' => $jobDesignId,
            'user_id' => $usertoken,
        ];

        # Insert data into the table
        if (!empty($data)) {
            DB::table('save_designs')->insert($data);
        }

        return Utility::outputData(true, "Design saved successfully", [], 201);
    }

    public function getSavedDesigns($user): \Illuminate\Http\JsonResponse
    {
        # Fetch saved designs  records associated with the user
        $savedDesigns = DB::table('save_designs')
            ->where('user_id', $user->id)
            ->get();

        #  Check if no records are found
        if ($savedDesigns->isEmpty()) {
            return Utility::outputData(false, 'No saved designs found for the user.', [], 404);
        }

        # Collection to store design objects
        $designObjects = collect();

        $savedDesigns->each(function ($savedDesigns) use ($designObjects) {
            $jobDesignId = $savedDesigns->job_design_id;

            # Fetch job object for the job post id and add to jobObjects
            $designObject = Design::select('id', 'project_title', 'project_price', 'created_at')
                ->find($jobDesignId);

            if ($designObject) {
                $designObjects->push($designObject);
            }
        });

        return Utility::outputData(true, 'Saved  designs fetched successfully.', (new DesignResource($designObjects))->toArraySavedJobs(), 200);
    }



    public function LikeDesign(int $jobDesignId, int $usertoken): \Illuminate\Http\JsonResponse
    {
        $recordExists  = Design::find($jobDesignId);
        if (!$recordExists) {
            return Utility::outputData(false, "Record could not be found", [], 400);
        }


        # Check if the user token already exists in the save_design_like table
        $existingRecord = DB::table('job_design_likes')
            ->where('job_design_id', $jobDesignId)
            ->where('user_id', $usertoken)
            ->exists();

        if ($existingRecord) {
            return Utility::outputData(false, "Design already liked by this user", [], 400);
        }

        # Prepare data for insertion
        $data =  [
            'job_design_id' => $jobDesignId,
            'user_id' => $usertoken,
        ];

        # Insert data into the table
        if (!empty($data)) {
            DB::table('job_design_likes')->insert($data);
        }

        # Update the like count in the designs table
        Design::where('id', $jobDesignId)->increment('likes');

        $designUrl = config('services.app_config.design_link_url')  . '/' . $jobDesignId;

        event(new LikeDesign($recordExists->user->email, $recordExists->user->fullname, $designUrl, $recordExists->project_title));

        return Utility::outputData(true, "Design liked", [], 201);
    }



}