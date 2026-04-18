@extends('layouts.layout')

@section('content')
    <div>
        <x-link url="/admin" text="Go to admin panel"></x-link>

        <div id="loader" style="display: none;">
            <x-loader></x-loader>
        </div>

        <form id="productForm">
            @csrf
            <input type="text" name="name" placeholder="Nombre" />
            <input type="text" name="description" placeholder="Descripción" />

            <div class="categories-container">
                <label for="categories-select">Asignar categoría:</label>
                <select id="categories-select" name="categories-id">
                    <option value="">Categoría...</option>
                </select>
            </div>

            <div class="subcategories-container d-none">
                <label for="subcategories-select">Asignar subcategoría:</label>
                <select id="subcategories-select" name="subcategories-id">
                    <option value="">Subcategoría...</option>
                </select>
            </div>

            <div>
                <span>Categorías añadidas:</span>
                <div id="added-categories-container" class="d-flex gap-2 flex-wrap"></div>
            </div>

            <div>
                <span>Subcategorías añadidas:</span>
                <div id="added-subcategories-container" class="d-flex gap-2 flex-wrap"></div>
            </div>

            <div>
                <span>Tarifa inicial:</span>
                <div>
                    <label for="start_day">Dia inicial:</label>
                    <input type="date" name="start_day" />
                </div>
                <div>
                    <label for="end_day">Dia final:</label>
                    <input type="date" name="end_day" />
                </div>
                <div>
                    <label for="price">Precio:</label>
                    <input type="number" name="price" min="0" placeholder="0" />
                </div>
            </div>
            <div>
                <label for="photo-input">Fotos del producto (máx. 3):</label>
                <input type="file" id="photo-input" accept="image/jpg,image/jpeg,image/png,image/webp" multiple />
                <small id="photo-count-msg"></small>
                <div id="photo-preview-container" class="d-flex gap-2 mt-2"></div>
            </div>

            <button type="submit">Crear nuevo producto</button>
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
                        '<option value="">Categoría...</option>' +
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
                        '<option value="">Subcategoría...</option>' +
                        filtered.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
                } else {
                    subcategoriesContainer.classList.add('d-none');
                    subcategoriesSelect.innerHTML = '<option value="">Subcategoría...</option>';
                }
            };

            const updateAssignedCategoriesScreen = () => {
                addedCategoriesContainer.innerHTML = '';
                assignedCategoriesId.forEach(catId => {
                    const cat = allCategories.find(c => c.id == catId);
                    if (!cat) return;
                    const div = document.createElement('div');
                    div.className = 'addedCategory';
                    div.innerHTML =
                        `${cat.name} <span style="cursor:pointer" data-id="${cat.id}" class="remove-cat">✕</span>`;
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
                    div.className = 'addedCategory';
                    div.innerHTML =
                        `${sub.name} <span style="cursor:pointer" data-id="${sub.id}" class="remove-sub">✕</span>`;
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
            let selectedFiles = []; // Array de File objects seleccionados

            photoInput.addEventListener('change', function() {
                const newFiles = Array.from(this.files);

                // Añadir solo hasta llegar al límite
                for (const file of newFiles) {
                    if (selectedFiles.length >= MAX_PHOTOS) break;
                    // Evitar duplicados por nombre
                    if (!selectedFiles.find(f => f.name === file.name)) {
                        selectedFiles.push(file);
                    }
                }

                // Limpiar el input para permitir volver a elegir el mismo archivo
                this.value = '';

                renderPhotoPreviews();
            });

            const renderPhotoPreviews = () => {
                photoPreview.innerHTML = '';

                const remaining = MAX_PHOTOS - selectedFiles.length;
                photoCountMsg.textContent = `${selectedFiles.length}/${MAX_PHOTOS} fotos seleccionadas`;
                photoInput.disabled = selectedFiles.length >= MAX_PHOTOS;

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const wrapper = document.createElement('div');
                        wrapper.style.cssText = 'position:relative;display:inline-block;';
                        wrapper.innerHTML =
                            `
                            <img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ccc;" />
                            <span data-index="${index}" class="remove-photo"
                                style="position:absolute;top:-6px;right:-6px;background:#dc3545;color:#fff;
                                       border-radius:50%;width:20px;height:20px;display:flex;
                                       align-items:center;justify-content:center;cursor:pointer;font-size:12px;">✕</span>`;
                        photoPreview.append(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            };

            // Evento delegado: quitar foto de la lista
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
                formData.append('categories', JSON.stringify(assignedCategoriesId));
                formData.append('subcategories', JSON.stringify(assignedSubcategoriesId));

                // Añadir cada foto individualmente como 'photos[]'
                selectedFiles.forEach(file => formData.append('photos[]', file));

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
                    console.error('Error:', err);
                    alert('Error al crear el producto: ' + JSON.stringify(err.errors ?? err.message));
                    loader.style.display = 'none';
                    form.style.display = 'block';
                }
            });
        </script>
    </div>
@endsection
