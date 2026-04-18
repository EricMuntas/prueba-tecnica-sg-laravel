<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('categories', 'subcategories')->get();

        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:30',
            'description' => 'required|string|max:255',
            'photos' => 'nullable|array|max:3',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'start_day' => 'required|date',
            'end_day' => 'required|date',
            'price' => 'required|numeric',
        ]);

        // Subir fotos (máximo 3)
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products', 'public');
                $photoPaths[] = Storage::url($path);
            }
        }

        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'photo_url'   => $photoPaths,
        ]);

        // Sync categorías
        if ($request->filled('categories')) {
            $categoryIds = is_array($request->categories)
                ? $request->categories
                : json_decode($request->categories, true);
            $product->categories()->sync(array_filter((array) $categoryIds));
        }

        // Sync subcategorías
        if ($request->filled('subcategories')) {
            $subcategoryIds = is_array($request->subcategories)
                ? $request->subcategories
                : json_decode($request->subcategories, true);
            $product->subcategories()->sync(array_filter((array) $subcategoryIds));
        }

        $fee = Fee::create([
            'product_id' => $product->id,
            'start_day' => $request->start_day,
            'end_day' => $request->end_day,
            'price' => $request->price,
        ]);

        return response()->json($product->load('categories', 'subcategories'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('categories', 'subcategories', 'fees')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        $request->validate([
            'name'            => 'sometimes|string|max:30',
            'description'     => 'sometimes|string|max:255',
            'photos'          => 'nullable|array',
            'photos.*'        => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photos'   => 'nullable|array',
            'remove_photos.*' => 'nullable|string',
        ]);

        // ── Gestión de fotos ──────────────────────────────────────────────
        $currentPhotos = $product->photo_url ?? [];

        // Eliminar fotos marcadas para borrado
        if ($request->filled('remove_photos')) {
            $toRemove = is_array($request->remove_photos)
                ? $request->remove_photos
                : json_decode($request->remove_photos, true);

            foreach ((array) $toRemove as $url) {
                $storagePath = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
                Storage::disk('public')->delete($storagePath);
            }
            $currentPhotos = array_values(array_diff($currentPhotos, (array) $toRemove));
        }

        // Añadir nuevas fotos (respetando el límite de 3)
        if ($request->hasFile('photos')) {
            $remaining = 3 - count($currentPhotos);
            $i = 0;
            foreach ($request->file('photos') as $photo) {
                if ($i >= $remaining) break;
                $path = $photo->store('products', 'public');
                $currentPhotos[] = Storage::url($path);
                $i++;
            }
        }

        // Guardar un máximo de 3 fotos
        if (count($currentPhotos) > 3) {
            return response()->json(['message' => 'No se pueden tener más de 3 fotos por producto.'], 422);
        }

        // ── Actualizar campos básicos ─────────────────────────────────────
        $product->update([
            'name'        => $request->input('name', $product->name),
            'description' => $request->input('description', $product->description),
            'photo_url'   => $currentPhotos,
        ]);

        // ── Sync categorías ───────────────────────────────────────────────
        if ($request->has('categories')) {
            $categoryIds = is_array($request->categories)
                ? $request->categories
                : json_decode($request->categories, true);
            $product->categories()->sync(array_filter((array) $categoryIds));
        }

        // ── Sync subcategorías ────────────────────────────────────────────
        if ($request->has('subcategories')) {
            $subcategoryIds = is_array($request->subcategories)
                ? $request->subcategories
                : json_decode($request->subcategories, true);
            $product->subcategories()->sync(array_filter((array) $subcategoryIds));
        }

        return response()->json($product->load('categories', 'subcategories'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        // Eliminar fotos del storage
        foreach ((array) ($product->photo_url ?? []) as $url) {
            $storagePath = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
            Storage::disk('public')->delete($storagePath);
        }

        // Desasociar relaciones pivot antes de borrar
        $product->categories()->detach();
        $product->subcategories()->detach();
        $product->delete();

        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }
}
