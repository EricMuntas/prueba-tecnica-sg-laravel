<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();

        return response()->json($orders, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'cost' => 'required|number',
        ]);

        $order = Order::create([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'cost' => $validated['cost'],
        ]);

        return response()->json($order, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::find($id);

        return response()->json($order, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'user_id' => 'integer',
            'date' => 'date',
            'cost' => 'number',
        ]);

        $order = Order::find($id);

        $order->update([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'cost' => $validated['cost'],
        ]);

        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::find($id)->delete();
        return response()->json(['message' => 'Pedido eliminado correctamente.']);
    }
}
