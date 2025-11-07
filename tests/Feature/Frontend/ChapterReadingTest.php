<?php

namespace Tests\Feature\Frontend;

use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChapterReadingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_reader_can_view_a_chapter_with_navigation_context(): void
    {
        $manga = Manga::factory()->create(['slug' => 'efsane-manga']);
        $previous = Chapter::factory()->forManga($manga)->create(['number' => 1]);
        $current = Chapter::factory()->forManga($manga)->create(['number' => 2]);
        $next = Chapter::factory()->forManga($manga)->create(['number' => 3]);

        $response = $this->get(route('chapters.show', [
            'manga' => $manga->slug,
            'number' => $current->number,
        ]));

        $response->assertOk()
            ->assertViewIs('chapters.show')
            ->assertViewHas('chapter', fn ($chapter) => $chapter->is($current))
            ->assertViewHas('previousChapter', fn ($chapter) => $chapter->is($previous))
            ->assertViewHas('nextChapter', fn ($chapter) => $chapter->is($next));

        $response->assertSee($current->title);
    }
}
