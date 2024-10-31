<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Auth\RegistrationController;
use \App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Warehouses
Route::get('/warehouses', [WarehouseController::class, 'index']);
Route::get('/warehouses/delete/{id}', [WarehouseController::class, 'delete']);
Route::get('/warehouses/get/{id}', [WarehouseController::class, 'get']);
Route::post('/warehouses/save', [WarehouseController::class, 'save']);
Route::post('/warehouses/update', [WarehouseController::class, 'update']);

//Stock
Route::get('/stocks', [StockController::class, 'index']);
Route::get('/stocks-movements', [StockController::class, 'getStockMovements']);

//Product
Route::get('/products', [ProductController::class, 'getProductsWithWarehouses']);



//Order
Route::get('/orders', [OrderController::class, 'showOrders']);
Route::post('/order-create', [OrderController::class, 'createOrder']);
Route::get('/orders/{orderId}', [OrderController::class, 'getOrder']);
Route::put('/orders/{orderId}', [OrderController::class, 'updateOrder']);
Route::delete('/orders-cancel/{orderId}', [OrderController::class, 'cancelOrder']);
Route::put('/orders-resume/{orderId}', [OrderController::class, 'resumeOrder']);
Route::put('/orders-complete/{orderId}', [OrderController::class, 'completeOrder']);




