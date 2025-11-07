<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chapter>
 */
class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    public function definition(): array
    {
        return [
            'manga_id' => Manga::factory(),
            'number' => $this->faker->unique()->numberBetween(1, 100),
            'title' => $this->faker->sentence(4),
            'pages' => collect(range(1, $this->faker->numberBetween(5, 12)))
                ->map(fn ($page) => sprintf('https://example.com/manga/%s/page-%s.jpg', $this->faker->uuid(), $page))
                ->all(),
        ];
    }

    public function forManga(Manga $manga): self
    {
        return $this->state(fn () => [
            'manga_id' => $manga->id,
        ]);
    }
}
