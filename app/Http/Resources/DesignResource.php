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






    public function toArrayWithMinimalData(): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        return $this->map(function ($design) {
            return [
                'job_design_id' => $design->id,
                'project_title' => $design['project_title'],
                'project_price' => $design->project_price ?? 0,
                'date_posted' => $design->created_at->diffForHumans(),
                 'views' => $design->view_count,
                 'likes' => $design->likes,
//                'project_types' => $job->job_type()->pluck('job_type.project_type')->toArray(),
                'user' => [
                    'client_name' => $design->user->fullname ?? null,
                    'current_location' => $design->user->country ?? null,
                    'profile_image' => 'https://via.placeholder.com/150'
                ],
                'images' => [
                    'image' => $design->images  #get the associated images related to the design
            ]
            ];
        });
    }
}
