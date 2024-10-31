<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockMovements;
use Illuminate\Http\Request;

class StockController extends Controller
{
   function index(Request $request){
       $perPage = $request->query('per_page', 10); // 5 по умолчанию

       $stocks = Stock::with('product', 'warehouse')
           ->select('stocks.product_id', 'stocks.warehouse_id', 'stocks.stock')
           ->paginate($perPage);

       return response(['status' => 'success', 'stocks' => $stocks, 'code' => 200]);
   }

    function getAll(Request $request){
        $stocks = Stock::with('product', 'warehouse')
            ->select('stocks.product_id', 'stocks.warehouse_id', 'stocks.stock')
            ->get();

        return response(['status' => 'success', 'stocks' => $stocks, 'code' => 200]);
    }

    public function getStockMovements(Request $request)
    {
        $movements = StockMovements::with('product', 'warehouse');

        // Фильтрация по складу
        if ($request->has('warehouse_id')) {
            $movements->where('warehouse_id', $request->warehouse_id);
        }

        // Фильтрация по товару
        if ($request->has('product_id')) {
            $movements->where('product_id', $request->product_id);
        }

        // Фильтрация по датам
        if ($request->has('start_date') && $request->has('end_date')) {
            $movements->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Пагинация
        $perPage = $request->query('per_page', 10);
        $movements = $movements->paginate($perPage);

        return response(['status' => 'success', 'movements' => $movements, 'code' => 200]);
    }
}
