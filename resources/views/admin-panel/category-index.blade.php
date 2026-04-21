@extends('layouts.layout')

@section('content')
<div class="container page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/admin" class="btn btn-outline-secondary btn-sm mb-2">← Volver a Admin</a>
            <h1 class="page-title mb-0">Gestión de Categorías</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/subcategories" class="btn btn-outline-primary">Ver subcategorías</a>
            <a href="/admin/categories/create" class="btn btn-primary">➕ Crear categoría</a>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Categoría</th>
                        <th>Descripción</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="category-tbody">
                    <tr><td colspan="3" class="text-center py-4 text-muted">Cargando categorías...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        fetch('/api/categories')
            .then(res => res.json())
            .then(categories => {
                const tbody = document.getElementById('category-tbody');

                if (categories.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">No existen categorías.</td></tr>';
                    return;
                }

                tbody.innerHTML = categories.map(category => `
                    <tr>
                        <td class="ps-4 fw-semibold text-primary-dark">${category.name}</td>
                        <td class="text-muted" style="font-size: 0.9rem;">${category.description ?? 'Sin descripción'}</td>
                        <td class="text-center pe-4">
                            <a href="/admin/categories/${category.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Editar</a>
                        </td>
                    </tr>
                `).join('');
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('category-tbody').innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error al cargar las categorías.</td></tr>';
            });
    </script>
</div>
@endsection
