<?php

namespace Database\Factories;

use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

class StationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Station::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'stream_url' => $this->faker->url(),
            'image_url' => $this->faker->imageUrl(100, 100),
            'description' => $this->faker->text(),
            'long_description' => $this->faker->text()
        ];
    }
}
