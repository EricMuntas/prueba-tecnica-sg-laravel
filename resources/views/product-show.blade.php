@extends('layouts.layout')

@section('content')
    <div>
        <x-link url="/" text="Go to dashboard"></x-link>

        <div id="products-container">
            <p>Cargando productos...</p>
        </div>

        <script>
            const product_id = @json($id);

            fetch('/api/products/' + product_id)
                .then(res => res.json())
                .then(product => {
                    const container = document.getElementById('products-container');
                    container.innerHTML = `
                <div>
                    <p>${product.name}</p>
                    <p>${product.price}</p>
                </div>
            `;
                })
                .catch(err => console.error('Error:', err));
        </script>

    </div>
@endsection
