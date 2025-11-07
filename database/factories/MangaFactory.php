<?php

namespace Database\Factories;

use App\Models\Manga;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Manga>
 */
class MangaFactory extends Factory
{
    protected $model = Manga::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'summary' => $this->faker->paragraph(),
            'genres' => $this->faker->randomElements([
                'Action', 'Adventure', 'Drama', 'Fantasy', 'Romance', 'Sci-Fi', 'Slice of Life', 'Sports'
            ], $this->faker->numberBetween(1, 3)),
            'cover_image_path' => null,
            'status' => $this->faker->randomElement(['ongoing', 'completed', 'hiatus']),
            'author' => $this->faker->name(),
            'artist' => $this->faker->name(),
            'published_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
