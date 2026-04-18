@extends('layouts.layout')

@section('content')
    <x-link url="/admin" text="Go to admin panel"></x-link>
    <x-link url="/admin/subcategories" text="Subcategories"></x-link>
    <x-link url="/admin/categories/create" text="CREATE CATEGORY"></x-link>
    <br>
    <div id="categories-container">
        <p>Cargando categorias...</p>
    </div>

    <script>
        fetch('/api/categories')
            .then(res => res.json())
            .then(categories => {
                const container = document.getElementById('categories-container');

                console.log(categories);

                container.innerHTML = categories.map(category => `
                <div>
                    <a href="#">${category.name}</a>
                    <p>${category.description}</p>
                    <a href="/admin/categories/${category.id}">editar</a>
                </div>
            `).join('');
            })
            .catch(err => console.error('Error:', err));
    </script>
@endsection
