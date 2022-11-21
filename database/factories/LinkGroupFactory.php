<?php

namespace Database\Factories;

use App\LinkGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class LinkGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LinkGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'hash' => Str::random(10),
            'user_id' => $this->faker->numberBetween(1, 100),
            'public' => true,
            'rotator' => false,
        ];
    }
}
