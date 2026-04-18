@extends('layouts.layout')

@section('content')
    <x-link url="/admin" text="Go to admin panel"></x-link>
    <x-link url="/admin/products/create" text="CREATE PRODUCT"></x-link>
    <br>
    <div id="product-container">
        <p>Cargando productos...</p>
    </div>

    <script>
        fetch('/api/products')
            .then(res => res.json())
            .then(product => {
                const container = document.getElementById('product-container');

                console.log(product);

                container.innerHTML = product.map(product => `
                <div>
                    <a href="#">${product.name}</a>
                    <p>${product.description}</p>
                    
                    <a href="/admin/products/${product.id}">editar</a>
                </div>
            `).join('');
            })
            .catch(err => console.error('Error:', err));
    </script>
@endsection
