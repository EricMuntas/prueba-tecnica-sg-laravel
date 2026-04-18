@extends('layouts.layout')

@section('content')
    <div>
        <x-link url="/admin" text="Go to admin panel"></x-link>
        <x-link url="/admin/products" text="go back"></x-link>

        <div id="products-container">
            <p>Cargando producto...</p>
        </div>

        <div id="loader" style="display: none;">
            <x-loader></x-loader>
        </div>

        <form id="productForm">
            @csrf
            <input id="form-name" type="text" name="name" placeholder="Nombre" />
            <input id="form-description" type="text" name="description" placeholder="Descripción" />


            <button type="submit">Actualizar</button>
        </form>

        <div id="deleteBtnContainer"></div>


        <script>
            const categoryId = @json($id);

            const deleteBtnContainer = document.getElementById('deleteBtnContainer');

            deleteBtnContainer.innerHTML = ` <button onclick="deleteItem('category', ${categoryId})">Borrar</button>`;


            // get data para presvisualizar los datos
            fetch('/api/categories/' + categoryId)
                .then(res => res.json())
                .then(product => {

                    let formName = document.getElementById('form-name');
                    formName.value = product.name;

                    let formDescription = document.getElementById('form-description');
                    formDescription.value = product.description;

                })
                .catch(err => console.error('Error:', err));



            const form = document.getElementById('productForm');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Mostrar loader, ocultar form
                loader.style.display = 'block';
                form.style.display = 'none';

                const formData = new FormData(this);
                formData.append('_method', 'PUT'); // Laravel requiere POST + _method=PUT para leer FormData

                console.log(formData);

                const response = await fetch(`/api/products/${categoryId}`, {
                    method: 'POST', // Usamos POST aquí
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();
                console.log(data);

                if (response.ok) {
                    alert('Producto actualizado!');
                    window.location.href = '/admin/products';
                } else {
                    alert('Error al actualizar: ' + (data.message || 'Verifica los datos.'));
                }

                loader.style.display = 'none';
                form.style.display = 'block';
            });
        </script>

    </div>
@endsection
