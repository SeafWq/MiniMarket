<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Случайным образом выбираем склад
        $warehouse = Warehouse::inRandomOrder()->first();

        return [
            'customer' => $this->faker->name,
            'warehouse_id' => $warehouse->id,
        ];
    }
}
