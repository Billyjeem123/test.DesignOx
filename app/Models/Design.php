<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    public  $table = 'job_designs';

    protected  $fillable  =  [
        'talent_id', 'project_title', 'project_desc', 'project_price','attachment', 'downloadable_file'
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
        return $this->hasMany(Images::class, 'job_design_id');
    }
}
