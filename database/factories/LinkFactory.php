<?php

namespace Database\Factories;

use App\Link;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'long_url' => $this->faker->url,
            'hash' => Str::random(10),
            'user_id' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(10),
            'type' => Arr::random(['frame', 'direct', 'overlay', 'splash']),
        ];
    }
}
