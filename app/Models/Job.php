<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job_posts';

    protected  $fillable = ['client_id', 'project_desc', 'project_type', 'tools_used', 'budget', 'duration',
        'experience_level', 'numbers_of_proposals', 'project_link_attachment', 'payment_channel', 'work_start_time', 'work_end_time'];


    
}
