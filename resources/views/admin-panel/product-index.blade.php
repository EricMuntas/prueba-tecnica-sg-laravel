@extends('layouts.layout')

@section('content')
    <x-link url="/admin" text="Go to admin panel"></x-link>
    <x-link url="/admin/products/create" text="CREATE PRODUCT"></x-link>
    <br>
    <div id="product-container">
        <p>Cargando productos...</p>
    </div>
    <div>
        <button id="export-btn" disabled>
            Exportar a XLS
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        let productData = [];

        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                productData = products;

                const container = document.getElementById('product-container');

                container.innerHTML = products.map(product => `
                <div>
                    <a href="#">${product.name}</a>
                    <p>${product.description}</p>
                    <a href="/admin/products/${product.id}">editar</a>
                </div>
            `).join('');

                document.getElementById('export-btn').disabled = false;
            })
            .catch(err => console.error('Error:', err));

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
@endsection
