<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display orders for the authenticated user.
     */
    public function index()
    {
        $orders = Order::with('products.currentFee')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($orders, 200);
    }

    /**
     * Store a newly created order with its products.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'cost'         => 'required|numeric|min:0',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'date'    => $validated['date'],
            'cost'    => $validated['cost'],
        ]);

        // Attach products with quantity via pivot
        $syncData = [];
        foreach ($validated['items'] as $item) {
            $syncData[$item['product_id']] = ['quantity' => $item['quantity']];
        }
        $order->products()->sync($syncData);

        return response()->json($order->load('products'), 201);
    }

    /**
     * Show a single order (only owner or admin).
     */
    public function show(string $id)
    {
        $order = Order::with('products.currentFee')->findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()?->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($order, 200);
    }

    /**
     * Update order (only owner).
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'date'               => 'sometimes|date',
            'cost'               => 'sometimes|numeric|min:0',
            'items'              => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|integer|exists:products,id',
            'items.*.quantity'   => 'required_with:items|integer|min:1',
        ]);

        $order->update(array_filter([
            'date' => $validated['date'] ?? null,
            'cost' => $validated['cost'] ?? null,
        ]));

        if (!empty($validated['items'])) {
            $syncData = [];
            foreach ($validated['items'] as $item) {
                $syncData[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $order->products()->sync($syncData);

            // Recalculate cost from products
            $order->refresh()->load('products.currentFee');
            $newCost = $order->products->sum(fn($p) =>
                ($p->currentFee?->price ?? 0) * $p->pivot->quantity
            );
            $order->update(['cost' => $newCost]);
        }

        return response()->json($order->load('products.currentFee'), 200);
    }

    /**
     * Delete an order (only owner or admin).
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()?->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order->products()->detach();
        $order->delete();

        return response()->json(['message' => 'Pedido eliminado correctamente.']);
    }
}
