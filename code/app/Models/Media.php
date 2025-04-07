<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'collection',
        'disk',
        'path',
        'mime_type',
        'original_name',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected $casts = [
        'model_id' => 'integer',
    ];
}
