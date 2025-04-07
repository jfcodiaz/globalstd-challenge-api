<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserAvatarAssignControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->user = User::factory()->create();
    }

    public function test_assigns_avatar_successfully()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->store('uploads', 'local');

        $media = Media::factory()->create([
            'model_type' => User::class,
            'model_id' => $this->user->id,
            'path' => $path,
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
        ]);

        $response = $this->actingAs($this->user)->patchJson(
            route('user.avatar.assign'),
            ['media_id' => $media->id]
        );

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'avatar_id' => $media->id,
        ]);
    }

    public function test_fails_if_avatar_does_not_exist()
    {
        $response = $this->actingAs($this->user)->patchJson(
            route('user.avatar.assign'),
            ['media_id' => 9999]
        );

        $response->assertStatus(422);
    }

    public function test_fails_if_media_does_not_belong_to_user()
    {
        $otherUser = User::factory()->create();

        $media = Media::factory()->create([
            'model_type' => User::class,
            'model_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->patchJson(
            route('user.avatar.assign'),
            ['media_id' => $media->id]
        );

        $response->assertForbidden();
    }

    public function test_fails_without_authentication()
    {
        $media = Media::factory()->create();

        $response = $this->patchJson(
            route('user.avatar.assign'),
            ['avatar_id' => $media->id]
        );

        $response->assertUnauthorized();
    }
}
