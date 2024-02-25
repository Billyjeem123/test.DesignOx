<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    private mixed $user_id;
    private mixed $project_desc;
    private mixed $budget;
    private mixed $duration;
    private mixed $numbers_of_proposals;
    private mixed $project_link_attachment;
    private mixed $on_going;
    private mixed $experience_level;
    private mixed $tools;
    private mixed $keywords;
    private mixed $project_type;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client_id' => $this->user_id, // Assuming user_id is the client_id
            'project_desc' => $this->project_desc,
            'project_type' => $this->project_type,
            'budget' => $this->budget,
            'duration' => $this->duration,
            'experience_level' => $this->experience_level,
            'numbers_of_proposals' => $this->numbers_of_proposals,
            'project_link_attachment' => $this->project_link_attachment,
            'on_going' => $this->on_going == 0 ? 'pending' : ($this->on_going == 1 ? 'done' : 'unknown'),
            'tools' => $this->tools,
            'keywords' => $this->keywords
        ];
    }
}
