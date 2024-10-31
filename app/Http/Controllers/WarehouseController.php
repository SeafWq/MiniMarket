<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    function index()
    {
        return response(['status' => 'success', 'warehouses' => Warehouse::all(), 'code' => 200]);
    }

    function save(Request $request)
    {
        $request->validate(['name' => 'required']);
        $warehouse = Warehouse::create($request->all());

        return response(['status' => 'success', 'warehouse' => $warehouse, 'code' => 200]);
    }


    function get($id)
    {
        $warehouse = Warehouse::find($id);
        return response(['status' => 'success', 'warehouse' => $warehouse, 'code' => 200]);
    }

    function update(Request $request)
    {
        $request->validate(['name' => 'required']);
        $warehouse = Warehouse::find($request->id);
        $warehouse->update([
            'name' => $request->name,
        ]);

        return response(['status' => 'success', 'warehouse' => $warehouse, 'code' => 200]);
    }

    function delete($id)
    {
        $warehouse = Warehouse::find($id);
        $warehouse->delete();
        return response(['status' => 'success', 'message' => 'deleted successfully', 'code' => 200]);
    }
}
