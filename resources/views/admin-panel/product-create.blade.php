@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/products" class="btn btn-outline-secondary btn-sm me-3">← Volver a Productos</a>
        <h1 class="page-title mb-0">Crear Producto</h1>
    </div>

    <div id="loader" class="text-center py-5" style="display: none;">
        <div class="spinner"></div>
        <p class="text-muted mt-2">Guardando producto...</p>
    </div>

    <form id="productForm" class="card p-4 shadow-sm border-0">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="name" class="form-control" placeholder="Bicicleta de montaña..." required />
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <input type="text" name="description" class="form-control" placeholder="Ligera y resistente..." required />
            </div>
        </div>

        <hr class="my-4 text-muted">

        <h5 class="fw-bold mb-3 text-primary-dark">Clasificación</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="categories-select" class="form-label mb-1">Añadir categoría:</label>
                <select id="categories-select" name="category_id" class="form-select">
                    <option value="">Cargando categorías...</option>
                </select>
                <div class="mt-2">
                    <span class="text-muted small">Categorías añadidas:</span>
                    <div id="added-categories-container" class="d-flex gap-2 flex-wrap mt-1"></div>
                </div>
            </div>

            <div class="col-md-6 subcategories-container d-none">
                <label for="subcategories-select" class="form-label mb-1">Añadir subcategoría (opcional):</label>
                <select id="subcategories-select" name="subcategory_id" class="form-select">
                    <option value="">Subcategoría...</option>
                </select>
                <div class="mt-2">
                    <span class="text-muted small">Subcategorías añadidas:</span>
                    <div id="added-subcategories-container" class="d-flex gap-2 flex-wrap mt-1"></div>
                </div>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="p-3 mb-4 rounded" style="background:#f8f9ff; border:1px solid #eef2ff;">
            <h5 class="fw-bold mb-3 text-primary-dark text-center">Tarifa Inicial</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_day" class="form-label text-muted mb-1" style="font-size:0.85rem; font-weight:600; text-transform:uppercase;">Día inicial:</label>
                    <input type="date" name="start_day" class="form-control form-control-sm" required />
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_day" class="form-label text-muted mb-1" style="font-size:0.85rem; font-weight:600; text-transform:uppercase;">Día final:</label>
                    <input type="date" name="end_day" class="form-control form-control-sm" required />
                </div>
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label text-primary-dark mb-1 fw-bold">Precio (€):</label>
                    <input type="number" name="price" min="0" step="0.01" class="form-control form-control-sm" placeholder="0.00" required />
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="photo-input" class="form-label fw-semibold">Fotos del producto (máx. 3):</label>
            <input type="file" id="photo-input" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp" multiple />
            <small id="photo-count-msg" class="text-muted mt-1 d-block"></small>
            <div id="photo-preview-container" class="d-flex gap-2 mt-2"></div>
        </div>

        <p id="error-message-container" class="text-danger fw-semibold" style="font-size:0.9rem;"></p>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">Crear nuevo producto</button>
        </div>
    </form>

    <script>
        const categoriesSelect = document.getElementById('categories-select');
        const subcategoriesSelect = document.getElementById('subcategories-select');
        const subcategoriesContainer = document.querySelector('.subcategories-container');
        const addedCategoriesContainer = document.getElementById('added-categories-container');
        const addedSubcategoriesContainer = document.getElementById('added-subcategories-container');

        let allCategories = [];
        let allSubcategories = [];
        let assignedCategoriesId = [];
        let assignedSubcategoriesId = [];

        fetch('/api/categories')
            .then(res => res.json())
            .then(data => {
                allCategories = data;
                categoriesSelect.innerHTML =
                    '<option value="">Seleccionar Categoría...</option>' +
                    data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            })
            .catch(err => console.error('Error categorías:', err));

        fetch('/api/subcategories')
            .then(res => res.json())
            .then(data => {
                allSubcategories = data;
            })
            .catch(err => console.error('Error subcategorías:', err));

        categoriesSelect.addEventListener('change', function() {
            const catId = this.value;
            if (catId && !assignedCategoriesId.includes(catId)) {
                assignedCategoriesId.push(catId);
                updateAssignedCategoriesScreen();
                updateSubcategoriesDropdown();
            }
            this.value = '';
        });

        subcategoriesSelect.addEventListener('change', function() {
            const subId = this.value;
            if (subId && !assignedSubcategoriesId.includes(subId)) {
                assignedSubcategoriesId.push(subId);
                updateAssignedSubcategoriesScreen();
            }
            this.value = '';
        });

        const updateSubcategoriesDropdown = () => {
            const filtered = allSubcategories.filter(sub =>
                assignedCategoriesId.includes(String(sub.category_id))
            );

            if (filtered.length > 0) {
                subcategoriesContainer.classList.remove('d-none');
                subcategoriesSelect.innerHTML =
                    '<option value="">Seleccionar Subcategoría...</option>' +
                    filtered.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            } else {
                subcategoriesContainer.classList.add('d-none');
                subcategoriesSelect.innerHTML = '<option value="">Seleccionar Subcategoría...</option>';
            }
        };

        const updateAssignedCategoriesScreen = () => {
            addedCategoriesContainer.innerHTML = '';
            assignedCategoriesId.forEach(catId => {
                const cat = allCategories.find(c => c.id == catId);
                if (!cat) return;
                const div = document.createElement('div');
                div.className = 'badge bg-primary d-flex align-items-center gap-2 px-2 py-1';
                div.innerHTML =
                    `${cat.name} <span style="cursor:pointer" data-id="${cat.id}" class="remove-cat bg-danger rounded-circle px-1" title="Eliminar">✕</span>`;
                addedCategoriesContainer.append(div);
            });
        };

        addedCategoriesContainer.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-cat')) return;
            const id = e.target.dataset.id;

            assignedCategoriesId = assignedCategoriesId.filter(c => c != id);
            assignedSubcategoriesId = assignedSubcategoriesId.filter(subId => {
                const sub = allSubcategories.find(s => s.id == subId);
                return sub && String(sub.category_id) != id;
            });

            updateAssignedCategoriesScreen();
            updateAssignedSubcategoriesScreen();
            updateSubcategoriesDropdown();
        });

        const updateAssignedSubcategoriesScreen = () => {
            addedSubcategoriesContainer.innerHTML = '';
            assignedSubcategoriesId.forEach(subId => {
                const sub = allSubcategories.find(s => s.id == subId);
                if (!sub) return;
                const div = document.createElement('div');
                div.className = 'badge bg-secondary d-flex align-items-center gap-2 px-2 py-1';
                div.innerHTML =
                    `${sub.name} <span style="cursor:pointer" data-id="${sub.id}" class="remove-sub bg-danger rounded-circle px-1" title="Eliminar">✕</span>`;
                addedSubcategoriesContainer.append(div);
            });
        };

        addedSubcategoriesContainer.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-sub')) return;
            const id = e.target.dataset.id;
            assignedSubcategoriesId = assignedSubcategoriesId.filter(s => s != id);
            updateAssignedSubcategoriesScreen();
        });

        const photoInput = document.getElementById('photo-input');
        const photoPreview = document.getElementById('photo-preview-container');
        const photoCountMsg = document.getElementById('photo-count-msg');
        const MAX_PHOTOS = 3;
        let selectedFiles = []; 

        photoInput.addEventListener('change', function() {
            const newFiles = Array.from(this.files);

            for (const file of newFiles) {
                if (selectedFiles.length >= MAX_PHOTOS) break;
                if (!selectedFiles.find(f => f.name === file.name)) {
                    selectedFiles.push(file);
                }
            }
            this.value = '';
            renderPhotoPreviews();
        });

        const renderPhotoPreviews = () => {
            photoPreview.innerHTML = '';

            photoCountMsg.textContent = `${selectedFiles.length}/${MAX_PHOTOS} fotos seleccionadas`;
            photoInput.disabled = selectedFiles.length >= MAX_PHOTOS;

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position:relative;display:inline-block;';
                    wrapper.innerHTML =
                        `
                        <img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:1px solid #ccc; box-shadow: 0 4px 6px rgba(0,0,0,0.05);" />
                        <span data-index="${index}" class="remove-photo"
                            style="position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;
                                   border-radius:50%;width:22px;height:22px;display:flex;
                                   align-items:center;justify-content:center;cursor:pointer;font-size:12px; font-weight:bold;">✕</span>`;
                    photoPreview.append(wrapper);
                };
                reader.readAsDataURL(file);
            });
        };

        photoPreview.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-photo')) return;
            const index = parseInt(e.target.dataset.index);
            selectedFiles.splice(index, 1);
            renderPhotoPreviews();
        });

        const loader = document.getElementById('loader');
        const form = document.getElementById('productForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            loader.style.display = 'block';
            form.style.display = 'none';

            const formData = new FormData(this);
            formData.append('assigned_categories', JSON.stringify(assignedCategoriesId));
            formData.append('assigned_subcategories', JSON.stringify(assignedSubcategoriesId));

            if (formData.get('name').trim() == '') {
                sendErrorMessage('nombre');
                return;
            }
            if (formData.get('description').trim() == '') {
                sendErrorMessage('descripción');
                return;
            }
            if (assignedCategoriesId.length === 0) {
                sendErrorMessage('categoría');
                return;
            }
            if (formData.get('start_day').trim() == '') {
                sendErrorMessage('dia inicial');
                return;
            }
            if (formData.get('end_day').trim() == '') {
                sendErrorMessage('dia final');
                return;
            }
            if (formData.get('price').trim() == '') {
                sendErrorMessage('precio');
                return;
            }

            selectedFiles.forEach(file => formData.append('photos[]', file));

            try {
                const res = await fetch('/api/products', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (res.ok) {
                    window.location.href = '/admin/products';
                } else {
                    const err = await res.json();
                    throw new Error(JSON.stringify(err.errors ?? err.message));
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error al crear el producto: ' + err.message);
                loader.style.display = 'none';
                form.style.display = 'block';
            }
        });

        const sendErrorMessage = (field) => {
            loader.style.display = 'none';
            form.style.display = 'block';
            const errorMessageContainer = document.getElementById('error-message-container');
            const errorMessage = 'Revisión necesaria: No puedes dejar el campo ' + field + ' vacío.';
            errorMessageContainer.innerHTML = errorMessage;
        }
    </script>
</div>
@endsection
