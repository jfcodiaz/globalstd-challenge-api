<?php

namespace Tests\Feature\Http\Controllers\Media;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->user = User::factory()->create();
    }

    public function test_upload_avatar_successfully(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)->postJson(
            route('media.store'),
            [
                'collection' => 'avatar',
                'file' => $file,
            ]
        );

        $response->assertCreated();

        $this->assertDatabaseHas('media', [
            'collection' => 'avatar',
            'original_name' => 'avatar.jpg',
        ]);
    }

    public function test_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson(route('media.store'), [
            'collection' => 'avatar',
            'file' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_avatar_upload_requires_valid_file_type(): void
    {
        $file = UploadedFile::fake()->create(
            'avatar.pdf', 100, 'application/pdf'
        );

        $response = $this->actingAs($this->user)->postJson(
            route('media.store'),
            [
                'collection' => 'avatar',
                'file' => $file,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_fails_if_collection_is_missing(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)->postJson(
            route('media.store'),
            ['file' => $file]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('collection');
    }
}
