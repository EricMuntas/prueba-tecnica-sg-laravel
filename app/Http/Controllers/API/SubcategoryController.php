<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subcategories = Subcategory::all();

        return response()->json($subcategories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:30',
            'description' => 'required|string|max:255',
        ]);

        $subcategory = Subcategory::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return response()->json($subcategory, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subcategory = Subcategory::find($id);

        return response()->json($subcategory, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'category_id' => 'integer',
            'name' => 'string|max:30',
            'description' => 'string|max:255',
        ]);

        $subcategory = Subcategory::find($id);

        $subcategory->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return response()->json($subcategory, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subcategory = Subcategory::find($id)->delete();
        return response()->json(['message' => 'Subcategoria eliminada correctamente.']);
    }
}
