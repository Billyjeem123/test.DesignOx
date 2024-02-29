<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
     public  mixed $user_id;
    public mixed $project_desc;
    public mixed $budget;
    public mixed $duration;
    public mixed $numbers_of_proposals;
    public mixed $project_link_attachment;
    public mixed $on_going;
    public mixed $experience_level;
    public mixed $tools;
    public mixed $keywords;
    public mixed $project_type;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->map(function ($job) {
            $on_going = $job->on_going === 0 ? 'pending' : ($job->on_going === 1 ? 'approved' : 'finished');
            return [
                'job_post_id' => $job->id,
                'usertoken' => $job->client_id ?? 0,
                'project_desc' => $job->project_desc ?? null,
                'project_budget' => $job->budget ?? 0,
                'project_duration' => $job->duration ?? 0,
                'experience_level' => $job->experience_level ?? 0,
                'numbers_of_proposals' => $job->numbers_of_proposals ?? 0,
                'project_link_attachment' => $job->project_link_attachment ?? 0,
                'on_going' => $on_going,
                'project_tools' => $job->tools ?? [],
                'project_keywords' => $job->keywords ?? [],
                'project_type' => $job->project_type ?? null,
            ];
        });
    }




}
