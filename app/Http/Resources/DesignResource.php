<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
    public function toArraySavedJobs(): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        return $this->map(function ($job) {
            return [
                'job_design_id' => $job->id,
                'project_title' => $job->project_title,
                'project_price' => $job->project_price ?? 0,
                'date_posted' => $job->created_at->diffForHumans()
            ];
        });
    }
    public function toArrayWithMinimalData(): array
    {
        $currentUser = auth()->user();

        return $this->map(function ($design) use ($currentUser) {
            return [
                'job_design_id' => $design->id,
                'project_title' => $design['project_title'],
                'project_price' => $design->project_price ?? 0,
                'date_posted' => $design->created_at->diffForHumans(),
                'views' => $design->view_count,
                'likes' => $design->likes,
                'liked_by_user' => $design->likedByUsers->contains($currentUser),
                'user' => [
                    'user_name' => $design->user->fullname ?? null,
                    'current_location' => $design->user->country ?? null,
                    'profile_image' => 'https://via.placeholder.com/150'
                ],
                'images' => [
                    'image' => $design->images  #get the associated images related to the design
                ]
            ];
        })->all();
    }
}
