@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 600px;">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/categories" class="btn btn-outline-secondary btn-sm me-3">← Volver</a>
        <h1 class="page-title mb-0">Crear Categoría</h1>
    </div>

    <div id="loader" class="text-center py-5" style="display: none;">
        <div class="spinner"></div>
        <p class="text-muted mt-2">Guardando...</p>
    </div>

    <div class="card p-4">
        <form id="categoryForm">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" placeholder="Nombre" required />
            </div>
            
            <div class="mb-4">
                <label class="form-label">Descripción</label>
                <input type="text" name="description" class="form-control" placeholder="Descripción" required />
            </div>

            <div class="mb-3 p-3 rounded" style="background:#f8f9ff; border:1px solid #eef2ff;">
                <div class="form-check">
                    <input id="is-subcategory-box" class="form-check-input" type="checkbox" name="is-subcategory">
                    <label class="form-check-label fw-semibold" for="is-subcategory-box" style="color:#3730a3;">
                        Esta es una subcategoría
                    </label>
                </div>
            </div>

            <div id="father-category-container" class="mb-4 d-none p-3 rounded" style="background:#fff; border:1px dashed #c7d2fe;">
                <label for="father-category" class="form-label text-primary-dark">Selecciona la categoría padre:</label>
                <select name="category_id" class="form-select">
                    <option value="">Seleccionar categoría...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <p id="error-message-container" class="text-danger mb-3 fw-semibold" style="font-size:0.9rem;"></p>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">Crear</button>
            </div>
        </form>
    </div>

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
