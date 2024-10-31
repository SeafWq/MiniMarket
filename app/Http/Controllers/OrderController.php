<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function showOrders(Request $request)
    {
        $orders = Order::with('orderItems.product');

        // Фильтрация по статусу
        if ($request->has('status')) {
            $orders->where('status', $request->status);
        }

        // Фильтрация по клиенту
        if ($request->has('customer')) {
            $orders->where('customer', 'like', '%' . $request->customer . '%');
        }

        // Пагинация
        $perPage = $request->query('per_page', 10);
        $orders = $orders->paginate($perPage);

        return response()->json($orders);
    }

    public function getOrder(Request $request, $orderId) {
        $order = Order::with([
            'orderItems.product' => function ($query) {
                $query->with([
                    'stocks' => function ($query) {
                        $query->where('stocks.product_id', '=', $query->getModel()->getTable().'.id')
                            ->with('warehouse');
                    }
                ]);
            },
            'warehouse'
        ])->findOrFail($orderId);

        return response()->json($order);
    }

    public function createOrder(Request $request)
    {
        // Валидация запроса
        $request->validate([
            'customer' => 'required',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.count' => 'required|integer|min:1',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
        ]);

        // Создание заказа
        $order = Order::create([
            'customer' => $request->customer,
            'warehouse_id' => null
        ]);

        // Создание позиций заказа
        foreach ($request->items as $item) {
            // Проверка наличия товара на складе
            $stock = Stock::where('product_id', $item['product_id'])
                ->where('warehouse_id', $item['warehouse_id'])
                ->first();

            if ($stock && $stock->stock >= $item['count']) {
                // Списание товара со склада
                $stock->stock -= $item['count'];
                $stock->save();

                // Создание позиции заказа
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                    'warehouse_id' => $item['warehouse_id'], // Используем warehouse_id из массива items
                ]);

                StockMovements::create([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $item['warehouse_id'],
                    'movement_type' => 'out', // Списание со склада
                    'quantity' => $item['count'],
                ]);

            } else {
                // Ошибка: недостаточно товара на складе
                return response()->json(['error' => 'Not enough products in stock'], 400);
            }
        }

        return response()->json($order);
    }

    public function updateOrder(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Валидация запроса
        $request->validate([
            'customer' => 'required',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.count' => 'required|integer|min:1',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
        ]);

        // Обновление данных покупателя
        $order->update([
            'customer' => $request->customer,
        ]);

        // Обновление позиций заказа
        foreach ($request->items as $item) {
            // Проверка наличия товара на складе
            $stock = Stock::where('product_id', $item['product_id'])
                ->where('warehouse_id', $item['warehouse_id'])
                ->first();

            if ($stock && $stock->stock >= $item['count']) {
                // Списание товара со склада
                $stock->stock -= $item['count'];
                $stock->save();

                // Создание новой позиции заказа
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                    'warehouse_id' => $item['warehouse_id'],
                ]);
            } else {
                // Ошибка: недостаточно товара на складе
                return response()->json(['error' => 'Not enough products in stock'], 400);
            }
        }

        return response()->json($order);
    }

    public function completeOrder($orderId)
    {
            $order = Order::find($orderId);

            // Проверка существования заказа
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Завершение заказа
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return response()->json($order);
    }

    public function cancelOrder($orderId)
    {
        $order = Order::with('orderItems.product.stocks')->find($orderId); // Загружаем stocks через product

        // Проверка существования заказа
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Отмена заказа
        $order->update([
            'status' => 'canceled',
        ]);

        // Возврат товара на склад
        foreach ($order->orderItems as $orderItem) {
            $stock = $orderItem->product->stocks->first();
            if ($stock) {
                // Обновите stock
                $stock->update([
                    'stock' => $stock->stock + $orderItem->count,
                ]);

                StockMovements::create([
                    'product_id' => $orderItem->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'movement_type' => 'in',
                    'quantity' => $orderItem->count,
                ]);
            }
        }

        return response()->json($order);
    }

    public function resumeOrder($orderId)
    {
        $order = Order::with('orderItems.product.stocks')->find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Проверка, что заказ отменен
        if ($order->status !== 'canceled') {
            return response()->json(['error' => 'Order is not canceled'], 400);
        }

        foreach ($order->orderItems as $orderItem) {
            $stock = $orderItem->product->stocks->firstWhere('warehouse_id', $order->warehouse_id);
            if ($stock && $stock->stock >= $orderItem->count) {
                $stock->update([
                    'stock' => $stock->stock - $orderItem->count,
                ]);

                StockMovements::create([
                    'product_id' => $orderItem->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'movement_type' => 'out',
                    'quantity' => $orderItem->count,
                ]);
            } else {
                return response()->json(['error' => 'Not enough product in stock'], 400);
            }
        }

        // Если все товары есть в наличии, обновляем статус заказа
        $order->update([
            'status' => 'active',
        ]);

        return response()->json($order);
    }


}
