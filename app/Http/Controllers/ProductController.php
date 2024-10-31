<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    function index()
    {
        return response(['status' => 'success', 'products' => Product::all(), 'code' => 200]);
    }

    public function getProductsWithWarehouses(Request $request)
    {
        $products = Product::with('stocks.warehouse')->get();
        return response()->json(['products' => $products]);
    }



}
