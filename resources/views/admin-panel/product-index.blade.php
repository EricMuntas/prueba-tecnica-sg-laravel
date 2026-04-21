@extends('layouts.layout')

@section('content')
<div class="container page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="/admin" class="btn btn-outline-secondary btn-sm mb-2">← Volver a Admin</a>
            <h1 class="page-title mb-0">Gestión de Productos</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/products/create" class="btn btn-primary">➕ Crear producto</a>
            <button id="export-btn" class="btn btn-outline-primary" disabled>⬇️ Exportar a XLS</button>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 250px;">Producto</th>
                        <th>Descripción</th>
                        <th style="width: 100px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="product-tbody">
                    <tr><td colspan="3" class="text-center py-4 text-muted">Cargando productos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let productData = [];

        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                productData = products;
                const tbody = document.getElementById('product-tbody');

                if (products.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">No hay productos disponibles.</td></tr>';
                    return;
                }

                tbody.innerHTML = products.map(product => `
                    <tr>
                        <td class="ps-4 fw-semibold text-primary-dark">${product.name}</td>
                        <td class="text-muted" style="font-size: 0.9rem;">${product.description ?? 'Sin descripción'}</td>
                        <td class="text-center pe-4">
                            <a href="/admin/products/${product.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Editar</a>
                        </td>
                    </tr>
                `).join('');

                document.getElementById('export-btn').disabled = false;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('product-tbody').innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error al cargar listado.</td></tr>';
            });

        document.getElementById('export-btn').addEventListener('click', () => {
            if (!productData.length) return;

            const rows = productData.map(p => ({
                'ID': p.id,
                'Nombre': p.name,
                'Descripción': p.description,
                'Categorías': (p.categories ?? []).map(c => c.name).join(', '),
                'Subcategorías': (p.subcategories ?? []).map(s => s.name).join(', '),
                'Precio actual': p.current_fee ? p.current_fee.price : '',
                'Vigencia desde': p.current_fee ? p.current_fee.start_day : '',
                'Vigencia hasta': p.current_fee ? p.current_fee.end_day : '',
            }));

            const ws = XLSX.utils.json_to_sheet(rows);

            // Ancho de columnas automático
            const colWidths = Object.keys(rows[0]).map(key => ({
                wch: Math.max(key.length, ...rows.map(r => String(r[key] ?? '').length)) + 2
            }));
            ws['!cols'] = colWidths;

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Productos');

            const fecha = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `productos_${fecha}.xlsx`);
        });
    </script>
</div>
@endsection
