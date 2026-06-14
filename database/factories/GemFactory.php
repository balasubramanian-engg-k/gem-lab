<?php

namespace Database\Factories;

use App\Models\Gem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GemFactory extends Factory
{
    protected $model = Gem::class;

    public function definition()
    {
        return [
            'certificate_id' => 'CERT-' . strtoupper(Str::random(6)),
            'weight' => $this->faker->randomFloat(2, 1, 10),
            'unit' => $this->faker->randomElement(['carat', 'gram']),
            'stone' => $this->faker->randomElement(['Diamond', 'Ruby', 'Sapphire']),
            'variety' => $this->faker->word(),
            'color' => $this->faker->safeColorName(),
            'shape' => $this->faker->randomElement(['Oval', 'Round', 'Pear']),
            'dimension' => $this->faker->randomElement(['5x3 mm', '6x4 mm', '7x5 mm']),
            'date' => $this->faker->date(),
            'remarks' => $this->faker->sentence(6),
        ];
    }
}
