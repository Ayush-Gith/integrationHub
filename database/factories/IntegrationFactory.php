<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class IntegrationFactory extends Factory
{
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'user_id' => $user->id,
            'platform' => $this->faker->randomElement(['shopify', 'wix', 'woocommerce']),
            'api_key' => $this->faker->sha1(),
        ];
    }
}
