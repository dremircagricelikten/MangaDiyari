<?php

namespace Tests\Feature\Admin;

use App\Models\Manga;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MangaManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_create_a_manga_without_cover_image(): void
    {
        $user = User::factory()->create();
        $payload = [
            'title' => 'Deneme Manga',
            'slug' => 'deneme-manga',
            'summary' => 'Harika bir manga.',
            'genres' => 'Action, Adventure, Action',
            'status' => 'ongoing',
            'author' => 'Test Author',
            'artist' => 'Test Artist',
            'published_at' => Carbon::now()->toDateString(),
        ];

        $response = $this->actingAs($user)
            ->post(route('admin.mangas.store'), $payload);

        $response->assertRedirect(route('admin.mangas.index'))
            ->assertSessionHas('status', 'Manga başarıyla oluşturuldu.');

        $this->assertDatabaseHas('mangas', [
            'title' => 'Deneme Manga',
            'slug' => 'deneme-manga',
            'status' => 'ongoing',
        ]);

        $manga = Manga::where('slug', 'deneme-manga')->firstOrFail();
        $this->assertSame(['Action', 'Adventure'], $manga->genres);
    }
}
