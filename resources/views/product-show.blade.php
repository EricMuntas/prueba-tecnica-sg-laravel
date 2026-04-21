@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 720px;">
    <a href="/products" class="btn btn-outline-secondary btn-sm mb-4">← Volver al catálogo</a>

    <div id="products-container">
        <div class="text-center py-5">
            <div class="spinner"></div>
            <p class="text-muted mt-2">Cargando producto...</p>
        </div>
    </div>

    <script>
        const product_id = @json($id);

        fetch('/api/products/' + product_id)
            .then(res => res.json())
            .then(product => {
                const price  = product.current_fee ? parseFloat(product.current_fee.price).toFixed(2) : '—';
                const container = document.getElementById('products-container');
                container.innerHTML = `
                    <div class="card p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="font-size:3.5rem; line-height:1;">🛍️</div>
                            <div>
                                <h1 class="page-title mb-1">${product.name}</h1>
                                <div class="product-price" style="font-size:1.5rem;">${price}€</div>
                            </div>
                        </div>
                        <p class="text-muted">${product.description ?? 'Sin descripción.'}</p>
                        <hr>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <a href="/cart" class="btn btn-primary">🛒 Ir al carrito</a>
                            <a href="/products" class="btn btn-outline-secondary">← Catálogo</a>
                        </div>
                    </div>
                `;
            })
            .catch(err => console.error('Error:', err));
    </script>
</div>
@endsection
