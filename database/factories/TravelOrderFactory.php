<?php

namespace Database\Factories;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = TravelOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departureDate = fake()->dateTimeBetween('+1 day', '+30 days');
        $returnDate = fake()->dateTimeBetween($departureDate, '+40 days');

        return [
            'user_id'           => User::factory(),
            'city'              => fake()->city(),
            'state'             => fake()->state(),
            'country'           => fake()->country(),
            'departure_date'    => $departureDate->format('Y-m-d'),
            'return_date'       => $returnDate->format('Y-m-d'),
            'status'            => fake()->randomElement(['Solicitado', 'Aprovado', 'Cancelado']),
        ];
    }
}
