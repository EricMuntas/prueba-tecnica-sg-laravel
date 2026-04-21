@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 800px;">
    <h1 class="page-title mb-4">Panel de Administración</h1>

    <div class="products-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <a href="/admin/categories" class="card text-decoration-none text-dark h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📁</div>
                <h3 class="h5 fw-bold mb-0" style="color: #4f46e5;">Gestionar Categorías</h3>
                <p class="text-muted text-center mt-2 mb-0" style="font-size: 0.9rem;">Crea, edita o elimina las categorías y subcategorías del sistema.</p>
            </div>
        </a>

        <a href="/admin/products" class="card text-decoration-none text-dark h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                <h3 class="h5 fw-bold mb-0" style="color: #4f46e5;">Gestionar Productos</h3>
                <p class="text-muted text-center mt-2 mb-0" style="font-size: 0.9rem;">Añade nuevos productos, establece precios y organiza el catálogo.</p>
            </div>
        </a>
    </div>
</div>
@endsection
