@extends('layouts.layout')

@section('content')
    <x-link url="/admin" text="Go to admin panel"></x-link>
    <x-link url="/admin/categories" text="Categories"></x-link>
    <x-link url="/admin/categories/create" text="CREATE SUBCATEGORY"></x-link>
    <br>
    <div id="subcategories-container">
        <p>Cargando subcategorias...</p>
    </div>

    <script>
        fetch('/api/subcategories')
            .then(res => res.json())
            .then(subcategories => {
                const container = document.getElementById('subcategories-container');

                console.log(subcategories);

                container.innerHTML = subcategories.map(subcategory => `
                <div>
                    <a href="#">${subcategory.name}</a>
                    <p>${subcategory.description}</p>
                    <p>Categoria: ${subcategory.category.name}</p>
                    
                    <button onclick="deleteItem('subcategory', ${subcategory.id})">Borrar</button>
                </div>
            `).join('');
            })
            .catch(err => console.error('Error:', err));
    </script>
@endsection
