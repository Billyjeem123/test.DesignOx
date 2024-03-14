<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    public mixed $job_design_id;
    /**
     * @var bool|mixed
     */
    public mixed $path;
    protected $table = 'job_design_images';


    public function design(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Design::class, 'job_design_id');
    }
}
