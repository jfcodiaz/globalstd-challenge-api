<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'model_type' => null,
            'model_id' => null,
            'collection' => 'temp',
            'disk' => 'public',
            'path' => 'uploads/'.Str::random(10).'.jpg',
            'mime_type' => 'image/jpeg',
            'original_name' => $this->faker->word().'.jpg',
        ];
    }
}
