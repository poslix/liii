<?php

namespace Database\Factories;

use App\Link;
use App\LinkeableClick;
use Arr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkeableClickFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LinkeableClick::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'linkeable_id' => $this->faker->numberBetween(1, 500),
            'linkeable_type' => Link::class,
            'location' => $this->faker->countryCode,
            'ip' => $this->faker->ipv4,
            'platform' => Arr::random(['windows', 'linux', 'ios', 'androidos']),
            'device' => Arr::random(['mobile', 'tablet', 'desktop']),
            'crawler' => false,
            'browser' => Arr::random(['chrome', 'firefox', 'edge', 'internet exporer', 'safari']),
            'referrer' => $this->faker->url,
            'created_at' => $this->faker->dateTimeBetween(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()),
        ];
    }
}
