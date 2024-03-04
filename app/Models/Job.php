<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

//    public mixed $user_id;
    protected $table = 'tbljob_posts';

    protected  $fillable = ['client_id', 'project_desc', 'budget', 'duration',
        'experience_level', 'numbers_of_proposals', 'job_status', 'project_link_attachment', 'on_going', 'work_start_time', 'work_end_time'];


    /**
     * Relationship: a job belongs to a user (client)
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    
}
