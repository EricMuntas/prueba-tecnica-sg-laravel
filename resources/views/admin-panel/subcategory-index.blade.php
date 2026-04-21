@extends('layouts.layout')

@section('content')
<div class="container page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/admin/categories" class="btn btn-outline-secondary btn-sm mb-2">← Volver a Categorías</a>
            <h1 class="page-title mb-0">Gestión de Subcategorías</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/categories/create" class="btn btn-primary">➕ Crear subcategoría</a>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Subcategoría</th>
                        <th>Descripción</th>
                        <th>Categoría Padre</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="subcategory-tbody">
                    <tr><td colspan="4" class="text-center py-4 text-muted">Cargando subcategorías...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        fetch('/api/subcategories')
            .then(res => res.json())
            .then(subcategories => {
                const tbody = document.getElementById('subcategory-tbody');

                if (subcategories.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No existen subcategorías.</td></tr>';
                    return;
                }

                tbody.innerHTML = subcategories.map(s => `
                    <tr>
                        <td class="ps-4 fw-semibold text-primary-dark">${s.name}</td>
                        <td class="text-muted" style="font-size: 0.9rem;">${s.description ?? 'Sin descripción'}</td>
                        <td><span class="badge bg-light text-dark border">${s.category?.name ?? 'Ninguna'}</span></td>
                        <td class="text-center pe-4">
                            <a href="/admin/subcategories/${s.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Editar</a>
                        </td>
                    </tr>
                `).join('');
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('subcategory-tbody').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">Error al cargar subcategorías.</td></tr>';
            });
    </script>
</div>
@endsection
