<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'description' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'string|max:30',
            'description' => 'string|max:255',
        ]);

        $category = Category::find($id);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id)->delete();
        return response()->json(['message' => 'Categoria eliminada correctamente.']);
    }
}
