<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected  $fillable = [
      'talent_id', 'job_post_id', 'cover_letter', 'attachment', 'preferred_date'
    ];
}
