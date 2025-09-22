<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Integration;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $integration = Integration::inRandomOrder()->first() ?? Integration::factory()->create();

        return [
            'user_id' => $integration->user_id,
            'integration_id' => $integration->id,
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->bothify('SKU-####')),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'stock' => $this->faker->numberBetween(0, 100),
            'platform' => $integration->platform ?? $this->faker->randomElement(['shopify', 'wix', 'woocommerce']),
        ];
    }
}
