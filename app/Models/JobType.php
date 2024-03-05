<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    use HasFactory;
    protected $table = 'job_type';

    /**
     * Relationship: a jobType relates with many jobs
     */
   public function jobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
    return $this->belongsToMany(JobType::class, 'job_type_job_post', 'job_type_id', 'job_post_id');
  }

}
