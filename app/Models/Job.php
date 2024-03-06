<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

//    public mixed $user_id;
    protected $table = 'job_posts';

    protected $dates = [
        'last_viewed_at',
        'created_at'
    ];


    protected  $fillable = ['client_id', 'project_desc', 'project_title', 'budget', 'duration',
        'experience_level', 'numbers_of_proposals', 'job_status', 'project_link_attachment', 'on_going', 'work_start_time', 'work_end_time',
        'view_count','last_viewed_at'
        ];


    /**
     * Relationship: a job belongs to a user (client)
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relationship: a job has many Job_types
     */

    public function job_type(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(JobType::class, 'job_type_job_post', 'job_post_id', 'job_type_id');


    }
    /**
     * Relationship: a job has many proposals
     */

    public function proposals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Proposal::class, 'job_post_id');
    }



}
