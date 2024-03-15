<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    public  $table = 'job_designs';

    protected  $fillable  =  [
        'talent_id', 'project_title', 'project_desc', 'project_price','attachment', 'downloadable_file', 'view_count', 'likes', 'last_viewed_at'
        ];


    /**
     * Relationship: a  design has many  design_types
     */

    public function design_type(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'job_design_job_type', 'job_design_id', 'job_type_id');


    }
    /**
     * Relationship: a  design has many  Images
     */

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Images::class, 'job_design_id')->select('images'); #get specific columns
    }

    /**
     * Relationship: a design belongs to a user (client)
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_id')->select(['fullname', 'country']); #get specific columns
    }


    /**
     * Relationship: all  design liked by a user...
     */

    public function likedByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_design_likes', 'job_design_id', 'user_id');
    }
}
