<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    use HasFactory;
    protected $table = 'tbljob_post_types';

    /**
     * Relationship: a jobType relates with many jobs
     */
//    public function jobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(JobType::class, 'tbljob_post_types', 'job_type_id', 'job_post_id');
//    }
}
