<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Proposal extends Model
{
    use HasFactory;

    protected  $table ='job_proposal';

    protected  $fillable = [
      'talent_id', 'job_post_id', 'cover_letter', 'attachment', 'preferred_date'
    ];

    # proposal belongs to a job
    public function job(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

     #Proposal typically belongs to a User
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_id');
    }

}
