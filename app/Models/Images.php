<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    /**
     * @var bool|mixed
     */
    public mixed $path;
    /**
     * @var mixed|string
     */
    protected $table = 'job_design_images';

    protected $fillable = ['images', 'job_design_id'];

    public function design(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Design::class, 'job_design_id', 'id');
    }
}
