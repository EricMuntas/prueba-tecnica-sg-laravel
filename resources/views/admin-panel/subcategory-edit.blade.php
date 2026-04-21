@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 600px;">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/subcategories" class="btn btn-outline-secondary btn-sm me-3">← Volver</a>
        <h1 class="page-title mb-0">Editar Subcategoría</h1>
    </div>

    <div id="loader" class="text-center py-5" style="display: none;">
        <div class="spinner"></div>
        <p class="text-muted mt-2">Cargando...</p>
    </div>

    <div id="products-container" class="d-none"></div>

    <div class="card p-4 mb-4">
        <form id="productForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input id="form-name" class="form-control" type="text" name="name" placeholder="Cargando..." required />
            </div>
            
            <div class="mb-4">
                <label class="form-label">Descripción</label>
                <input id="form-description" class="form-control" type="text" name="description" placeholder="Cargando..." required />
            </div>

            <div class="mb-4 p-3 rounded" style="background:#f8f9ff; border:1px border #eef2ff;">
                <label for="categories-select" class="form-label text-primary-dark">Asignar categoría padre:</label>
                <select id="categories-select" name="category_id" class="form-select">
                    <option value="">Seleccionar Categoría...</option>
                </select>
            </div>

            <p id="error-message-container" class="text-danger mb-3 fw-semibold" style="font-size:0.9rem;"></p>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">Actualizar</button>
            </div>
        </form>
    </div>

    <div id="deleteBtnContainer" class="text-end"></div>

    <script>
        const subcategoryId = @json($id);
        const loader = document.getElementById('loader');
        const deleteBtnContainer = document.getElementById('deleteBtnContainer');

        deleteBtnContainer.innerHTML = `<button class="btn btn-outline-danger" onclick="deleteItem('subcategory', ${subcategoryId})">🗑️ Borrar Subcategoría</button>`;

        let allCategories = [];
        const categoriesSelect = document.getElementById('categories-select')

        const fetchSubcategory = fetch('/api/subcategories/' + subcategoryId).then(res => res.json());
        const fetchAllCategories = fetch('/api/categories').then(res => res.json());

        Promise.all([fetchSubcategory, fetchAllCategories])
            .then(([subcategory, categories]) => {
                document.getElementById('form-name').value = subcategory.name;
                document.getElementById('form-description').value = subcategory.description;

                categoriesSelect.innerHTML = categories.map(cat =>
                    `<option value="${cat.id}" ${cat.id == subcategory.category_id ? 'selected' : ''}>${cat.name}</option>`
                ).join('');
            })
            .catch(err => console.error('Error:', err));

        const form = document.getElementById('productForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            loader.style.display = 'block';
            form.style.display = 'none';

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            if (formData.get('name').trim() == '') {
                sendErrorMessage('nombre');
                return;
            }

            if (formData.get('description').trim() == '') {
                sendErrorMessage('descripción');
                return;
            }

            const response = await fetch(`/api/subcategories/${subcategoryId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = '/admin/subcategories';
            } else {
                alert('Error al actualizar: ' + (data.message || 'Verifica los datos.'));
            }

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
