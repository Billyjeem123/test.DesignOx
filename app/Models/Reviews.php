<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    protected  $table = 'job_design_reviews';

    protected  $fillable = [
        'user_id', 'job_design_id', 'reviews', 'ratings'
    ];

    /**
     * Relationship: a review belongs to a user
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->select(['fullname', 'country', 'email']); #get specific columns
    }

    /**
     * Relationship: Each reviews belongs to specific designs
     */
    public function design(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Design::class, 'job_design_id');
    }

}
