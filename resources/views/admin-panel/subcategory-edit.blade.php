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


            <div class="categories-container">
                <label for="categories-select">Asignar categoría:</label>
                <select id="categories-select" name="category_id">
                    <option value="">Categoría...</option>
                </select>
            </div>


            <p id="error-message-container"></p>

            <button type="submit">Actualizar</button>

        </form>

        <div id="deleteBtnContainer"></div>


        <script>
            const subcategoryId = @json($id);
            let thisCategory = '';

            const deleteBtnContainer = document.getElementById('deleteBtnContainer');

            deleteBtnContainer.innerHTML = ` <button onclick="deleteItem('subcategory', ${subcategoryId})">Borrar</button>`;

            // let thisSubcategoryData = null;
            let allCategories = [];
            const categoriesSelect = document.getElementById('categories-select')

            const fetchSubcategory = fetch('/api/subcategories/' + subcategoryId)
                .then(res => res.json());

            const fetchAllCategories = fetch('/api/categories')
                .then(res => res.json());

            Promise.all([fetchSubcategory, fetchAllCategories])
                .then(([subcategory, categories]) => {
                    thisSubcategoryData = subcategory;

                    // Rellenar inputs
                    document.getElementById('form-name').value = subcategory.name;
                    document.getElementById('form-description').value = subcategory.description;

                    // Rellenar select
                    categoriesSelect.innerHTML =
                        categories.map(cat =>
                            `<option value="${cat.id}" ${cat.id == subcategory.category_id ? 'selected' : ''}>
                    ${cat.name}
                </option>`
                        ).join('');
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

                if (formData.get('name').trim() == '') {
                    sendErrorMessage('nombre');
                    return;
                }

                if (formData.get('description').trim() == '') {
                    sendErrorMessage('descripción');
                    return;
                }


                console.log(formData);

                const response = await fetch(`/api/subcategories/${subcategoryId}`, {
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
                    window.location.href = '/admin/subcategories';
                } else {
                    alert('Error al actualizar: ' + (data.message || 'Verifica los datos.'));
                }

                loader.style.display = 'none';
                form.style.display = 'block';
            });



            /**
             * 
             * obtener categorias para poder cambiarla luego
             * 
             */

            const sendErrorMessage = (field) => {

                loader.style.display = 'none';
                form.style.display = 'block';

                const errorMessageContainer = document.getElementById('error-message-container');
                const errorMessage = 'No puedes dejar el campo ' + field + ' vacío.';

                errorMessageContainer.innerHTML = errorMessage;
            }
        </script>

    </div>
@endsection
