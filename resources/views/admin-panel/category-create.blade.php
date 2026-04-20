@extends('layouts.layout')

@section('content')
    <div>
        <x-link url="/admin" text="Go to admin panel"></x-link>

        <div id="loader" style="display: none;">
            <x-loader></x-loader>
        </div>

        <form id="categoryForm">
            @csrf
            <input type="text" name="name" placeholder="Nombre" />
            <input type="text" name="description" placeholder="Descripción" />
            <div>
                <input id="is-subcategory-box" type="checkbox" name="is-subcategory"> Es una subcategoria?
            </div>

            <div id="father-category-container" class="d-none">
                <label for="father-category">Selecciona una categoría:</label>
                <select name="category_id">
                    <option value="">Categoría...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>


            </div>

            <p id="error-message-container"></p>
            <button type="submit">Enviar</button>
        </form>

        <script>
            const form = document.getElementById('categoryForm');
            const loader = document.getElementById('loader');
            const is_subcategory_box = document.getElementById('is-subcategory-box');
            const father_category_container = document.getElementById('father-category-container');

            let api_url = '/api/categories';
            let is_subcategory = false;

            is_subcategory_box.addEventListener('click', async function(e) {

                is_subcategory = this.checked;

                if (is_subcategory) {
                    father_category_container.classList.remove('d-none');

                } else {
                    father_category_container.classList.add('d-none');
                }

                api_url = is_subcategory ? '/api/subcategories' : '/api/categories';

            });


            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Mostrar loader, ocultar form
                loader.style.display = 'block';
                form.style.display = 'none';


                const formData = new FormData(this);


                if (formData.get('name').trim() == '') {
                    sendErrorMessage('nombre');
                    return;
                }

                if (formData.get('description').trim() == '') {
                    sendErrorMessage('descripción');
                    return;
                }

                if (formData.get('is-subcategory') == 'on' && formData.get('category_id').trim() == '') {
                    sendErrorMessage('subcategoría');
                    return;
                }

                const response = await fetch(api_url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                window.location.href = is_subcategory ? "/admin/subcategories" : "/admin/categories";

                loader.style.display = 'none';
                form.style.display = 'block';
            });


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
