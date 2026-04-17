<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }


    public function product_index()
    {
        return view('product-index');
    }

    public function product_show(int $id)
    {
        return view('product-show', ['id' => $id]);
    }



    /**
     * 
     * 
     * 
     * ADMIN PANEL
     * 
     * 
     * 
     */
    public function admin_panel()
    {
        return view('admin-panel.admin-panel');
    }
    public function category_index()
    {
        return view('admin-panel.category-index');
    }

    // Mostrar la página con formulario para crear categorias o subcategorias
    public function category_create_index()
    {
        // Obtener categorias por si el user quiere hacer una subcategory
        $categories = Category::all();

        return view('admin-panel.category-create', ['categories' => $categories]);
    }

    public function subcategory_index()
    {
        return view('admin-panel.subcategory-index');
    }
}
