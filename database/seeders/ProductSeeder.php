<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Warehouse;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Создаем товары
        $products = Product::factory()->count(10)->create();

        // Создаем склады
        $warehouses = Warehouse::factory()->count(3)->create();

        // Создаем заказы и позиции
        foreach ($warehouses as $warehouse) {
            Order::factory()
                ->count(2) // Указываем количество заказов
                ->create([
                    'warehouse_id' => $warehouse->id,
                ])->each(function (Order $order) use ($products) {
                    $order->orderItems()->saveMany(
                        OrderItem::factory()->count(rand(1, 3))->make()->each(function (OrderItem $orderItem) use ($products) {
                            $orderItem->product_id = $products->random()->id;
                            $orderItem->count = rand(1, 5);
                        })
                    );
                });
        }

        // Создаем остатки на складах
        foreach ($warehouses as $warehouse) {
            $products->each(function (Product $product) use ($warehouse) {
                Stock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'stock' => rand(0, 20),
                ]);
            });
        }
    }
}
