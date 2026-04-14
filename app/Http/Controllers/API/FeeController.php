<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fees = Fee::all();

        return response()->json($fees, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'start_day' => 'required|date',
            'end_day' => 'required|date',
            'price' => 'required|number',
        ]);

        $fee = Fee::create([
            'product_id' => $validated['product_id'],
            'start_day' => $validated['start_day'],
            'end_day' => $validated['end_day'],
            'price' => $validated['price'],
        ]);

        return response()->json($fee, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fee = Fee::find($id);

        return response()->json($fee, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'product_id' => 'integer',
            'start_day' => 'date',
            'end_day' => 'date',
            'price' => 'number',
        ]);

        $fee = Fee::find($id);

        $fee->update([
            'product_id' => $validated['product_id'],
            'start_day' => $validated['start_day'],
            'end_day' => $validated['end_day'],
            'price' => $validated['price'],
        ]);

        return response()->json($fee, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fee = Fee::find($id)->delete();
        return response()->json(['message' => 'Fee eliminada correctamente.']);
    }
}
