<?php

namespace Tests\Feature\Http\Controllers\Media;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaShowControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->user = User::factory()->create();
        // $this->withoutExceptionHandling();
    }

    public function test_returns_original_file()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('media', 'local');

        $media = Media::factory()->create([
            'path' => $path,
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->user)
            ->get(route('media.show', ['media' => $media->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_returns_resized_image()
    {
        $file = UploadedFile::fake()->image('resize.jpg', 1000, 1000);
        $path = $file->store('media', 'local');

        $media = Media::factory()->create([
            'path' => $path,
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->user)
            ->get(
                route(
                    'media.show',
                    ['media' => $media->id, 'w' => 200, 'h' => 200]
                )
            )
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_returns_404_if_file_does_not_exist()
    {
        $media = Media::factory()->create([
            'path' => 'non/existing/path.jpg',
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->user)
            ->get(route('media.show', ['media' => $media->id]))
            ->assertNotFound();
    }

    public function test_redirects_to_s3_if_disk_is_s3()
    {
        Storage::shouldReceive('disk')->with('s3')->andReturnSelf();
        Storage::shouldReceive('exists')->andReturn(true);
        Storage::shouldReceive('url')->andReturn('https://s3-bucket/test.jpg');

        $media = Media::factory()->create([
            'path' => 'test.jpg',
            'disk' => 's3',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->user)
            ->get(route('media.show', ['media' => $media->id]))
            ->assertRedirect('https://s3-bucket/test.jpg');
    }

    public function test_fails_when_media_is_not_an_image()
    {
        $media = Media::factory()->create([
            'path' => 'file.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
        ]);

        $this->actingAs($this->user)
            ->get(route('media.show', [
                'media' => $media->id,
                'w' => 100,
                'h' => 100,
            ]))
            ->assertStatus(302);
    }

    public function test_fails_with_invalid_width_or_height()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('media', 'local');

        $media = Media::factory()->create([
            'path' => $path,
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->user)
            ->get(route('media.show', [
                'media' => $media->id,
                'w' => 'abc',
                'h' => -5,
            ]))
            ->assertStatus(302);
    }

    public function test_accepts_only_width_or_only_height()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('media', 'local');

        $media = Media::factory()->create([
            'path' => $path,
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->user)
            ->get(route('media.show', ['media' => $media->id, 'w' => 300]))
            ->assertOk();

        $this->actingAs($this->user)
            ->get(route('media.show', ['media' => $media->id, 'h' => 300]))
            ->assertOk();
    }
}
