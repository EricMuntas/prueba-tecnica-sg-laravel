@extends('layouts.layout')

@section('content')
    <x-link url="/" text="Go to dashboard"></x-link>
    <div id="products-container">
        <p>Cargando productos...</p>
    </div>

    <script>
        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                const container = document.getElementById('products-container');
                container.innerHTML = products.map(p => `
                <div>
                    <a href="/products/${p.id}">${p.name}</a>
                    <p>${p.price}</p>
                </div>
            `).join('');
            })
            .catch(err => console.error('Error:', err));
    </script>
@endsection
