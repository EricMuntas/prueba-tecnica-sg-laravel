<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{

    // MAIN
    public function index()
    {
        return view('admin-panel.admin-panel');
    }
    public function categoryIndex()
    {
        return view('admin-panel.category-index');
    }

    public function subcategoryIndex()
    {
        // Obtener categorias para mostrar en la tabla por category_id
        $categories = Category::select('id', 'name')->get();
        return view('admin-panel.subcategory-index', ['categories' => $categories]);
    }

    public function productIndex()
    {
        return view('admin-panel.product-index');
    }


    /**
     * 
     * 
     * CRUD
     * Paginas para crear,borrar,editar,etc
     * 
     * 
     */
    // Mostrar la página con formulario para crear categorias o subcategorias
    public function categoryCreateIndex()
    {
        // Obtener categorias por si el user quiere hacer una subcategory
        $categories = Category::all();

        return view('admin-panel.category-create', ['categories' => $categories]);
    }

    public function categoriesEditIndex(int $id)
    {
        // Obtener categorias por si el user quiere hacer una subcategory
        $categories = Category::all();

        return view('admin-panel.category-edit', ['id' => $id, 'categories' => $categories]);
    }



    public function productCreateIndex()
    {

        return view('admin-panel.product-create');
    }
    public function productEditIndex(int $id)
    {
        return view('admin-panel.product-edit', ['id' => $id]);
    }

    public function productFeesIndex(int $id)
    {

        return view('admin-panel.product-fees', ['id' => $id]);
    }

    public function feeEditIndex(int $id)
    {
        return view('admin-panel.fee-edit', ['id' => $id]);
    }
}
