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

            <div class="subcategories-container d-none">
                <label for="subcategories-select">Asignar subcategoría:</label>
                <select id="subcategories-select" name="subcategory_id">
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
                <label for="photo-input">Fotos del producto (máx. 3):</label>
                <input type="file" id="photo-input" accept="image/jpg,image/jpeg,image/png,image/webp" multiple />
                <small id="photo-count-msg"></small>
                <div id="photo-preview-container" class="d-flex gap-2 mt-2"></div>
            </div>

            <p id="error-message-container"></p>

            <button type="submit">Actualizar</button>
        </form>

        <div id="deleteBtnContainer"></div>
        <div>
            <button id="export-pdf-btn">
                Exportar a PDF
            </button>
        </div>
        <x-link url="/admin/products/{{ $id }}/fees" text="Ver fees"></x-link>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            const productId = @json($id);
            const deleteBtnContainer = document.getElementById('deleteBtnContainer');
            const loader = document.getElementById('loader');
            const form = document.getElementById('productForm');

            deleteBtnContainer.innerHTML = `<button onclick="deleteItem('product', ${productId})">Borrar</button>`;

            // ─── Estado ───────────────────────────────────────────────────────
            const categoriesSelect = document.getElementById('categories-select');
            const subcategoriesSelect = document.getElementById('subcategories-select');
            const subcategoriesContainer = document.querySelector('.subcategories-container');
            const addedCategoriesContainer = document.getElementById('added-categories-container');
            const addedSubcategoriesContainer = document.getElementById('added-subcategories-container');

            let allCategories = [];
            let allSubcategories = [];
            let assignedCategoriesId = [];
            let assignedSubcategoriesId = [];

            // ─── Carga inicial con Promise.all ────────────────────────────────
            Promise.all([
                    fetch('/api/products/' + productId).then(r => r.json()),
                    fetch('/api/categories').then(r => r.json()),
                    fetch('/api/subcategories').then(r => r.json()),
                ])
                .then(([product, categories, subcategories]) => {
                    allCategories = categories;
                    allSubcategories = subcategories;

                    // Prellenar inputs de texto
                    document.getElementById('form-name').value = product.name;
                    document.getElementById('form-description').value = product.description;
                    document.getElementById('products-container').innerHTML = '';

                    // Precargar categorías asignadas (product.categories debe ser array de {id, name})
                    if (Array.isArray(product.categories)) {
                        assignedCategoriesId = product.categories.map(c => String(c.id));
                    }

                    // Precargar subcategorías asignadas (product.subcategories debe ser array de {id, name})
                    if (Array.isArray(product.subcategories)) {
                        assignedSubcategoriesId = product.subcategories.map(s => String(s.id));
                    }

                    // Poblar select de categorías
                    categoriesSelect.innerHTML =
                        '<option value="">Categoría...</option>' +
                        categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                    // Renderizar lo ya asignado
                    updateAssignedCategoriesScreen();
                    updateAssignedSubcategoriesScreen();
                    updateSubcategoriesDropdown();

                    // Precargar fotos existentes (photo_url es array de URLs)
                    if (Array.isArray(product.photo_url)) {
                        existingPhotos = product.photo_url.map(url => ({
                            url
                        }));
                        renderPhotoPreviews();
                    }
                })
                .catch(err => console.error('Error carga inicial:', err));

            // ─── Categorías ───────────────────────────────────────────────────
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

            // ─── Fotos ────────────────────────────────────────────────────────
            const photoInput = document.getElementById('photo-input');
            const photoPreview = document.getElementById('photo-preview-container');
            const photoCountMsg = document.getElementById('photo-count-msg');
            const MAX_PHOTOS = 3;

            let selectedFiles = []; // Archivos nuevos (File objects)
            let existingPhotos = []; // Fotos ya guardadas { url }
            let deletedPhotoUrls = []; // URLs de fotos existentes a eliminar

            photoInput.addEventListener('change', function() {
                const newFiles = Array.from(this.files);
                const totalUsed = existingPhotos.length + selectedFiles.length;

                for (const file of newFiles) {
                    if (existingPhotos.length + selectedFiles.length >= MAX_PHOTOS) break;
                    if (!selectedFiles.find(f => f.name === file.name)) {
                        selectedFiles.push(file);
                    }
                }
                this.value = '';
                renderPhotoPreviews();
            });

            const renderPhotoPreviews = () => {
                photoPreview.innerHTML = '';
                const total = existingPhotos.length + selectedFiles.length;
                photoCountMsg.textContent = `${total}/${MAX_PHOTOS} fotos`;
                photoInput.disabled = total >= MAX_PHOTOS;

                // Fotos existentes (ya guardadas)
                existingPhotos.forEach((photo, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position:relative;display:inline-block;';
                    wrapper.innerHTML = `
                        <img src="${photo.url}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ccc;" />
                        <span data-existing-index="${index}" class="remove-existing-photo"
                            style="position:absolute;top:-6px;right:-6px;background:#dc3545;color:#fff;
                                   border-radius:50%;width:20px;height:20px;display:flex;
                                   align-items:center;justify-content:center;cursor:pointer;font-size:12px;">✕</span>`;
                    photoPreview.append(wrapper);
                });

                // Fotos nuevas (aún no subidas)
                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const wrapper = document.createElement('div');
                        wrapper.style.cssText = 'position:relative;display:inline-block;';
                        wrapper.innerHTML =
                            `
                            <img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #ccc;" />
                            <span data-index="${index}" class="remove-photo"
                                style="position:absolute;top:-6px;right:-6px;background:#6c757d;color:#fff;
                                       border-radius:50%;width:20px;height:20px;display:flex;
                                       align-items:center;justify-content:center;cursor:pointer;font-size:12px;">✕</span>`;
                        photoPreview.append(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            };

            photoPreview.addEventListener('click', (e) => {
                // Quitar foto nueva (gris)
                if (e.target.classList.contains('remove-photo')) {
                    const index = parseInt(e.target.dataset.index);
                    selectedFiles.splice(index, 1);
                    renderPhotoPreviews();
                }
                // Quitar foto existente (roja) → marcar para borrar en el backend
                if (e.target.classList.contains('remove-existing-photo')) {
                    const index = parseInt(e.target.dataset.existingIndex);
                    const removed = existingPhotos.splice(index, 1)[0];
                    if (removed.url) deletedPhotoUrls.push(removed.url);
                    renderPhotoPreviews();
                }
            });

            // ─── Submit ───────────────────────────────────────────────────────
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                loader.style.display = 'block';
                form.style.display = 'none';

                const formData = new FormData(this);
                formData.append('_method', 'PUT');
                formData.append('categories', JSON.stringify(assignedCategoriesId));
                formData.append('subcategories', JSON.stringify(assignedSubcategoriesId));
                // Enviar URLs de fotos a eliminar (el backend las recibe como remove_photos)
                deletedPhotoUrls.forEach(url => formData.append('remove_photos[]', url));

                if (formData.get('name').trim() === '') {
                    sendErrorMessage('nombre');
                    return;
                }
                if (formData.get('description').trim() === '') {
                    sendErrorMessage('descripción');
                    return;
                }
                if (assignedCategoriesId.length === 0) {
                    sendErrorMessage('categoría');
                    return;
                }

                selectedFiles.forEach(file => formData.append('photos[]', file));

                const response = await fetch(`/api/products/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Producto actualizado!');
                    window.location.href = '/admin/products';
                } else {
                    alert('Error al actualizar: ' + (data.message || 'Verifica los datos.'));
                    loader.style.display = 'none';
                    form.style.display = 'block';
                }
            });

            const sendErrorMessage = (field) => {
                loader.style.display = 'none';
                form.style.display = 'block';
                document.getElementById('error-message-container').innerHTML =
                    'No puedes dejar el campo ' + field + ' vacío.';
            };

            document.getElementById('export-pdf-btn').addEventListener('click', async () => {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();

                const name = document.getElementById('form-name').value;
                const description = document.getElementById('form-description').value;
                const categories = [...addedCategoriesContainer.querySelectorAll('.addedCategory')]
                    .map(el => el.textContent.replace('✕', '').trim()).join(', ');
                const subcats = [...addedSubcategoriesContainer.querySelectorAll('.addedCategory')]
                    .map(el => el.textContent.replace('✕', '').trim()).join(', ');

                // Cabecera
                doc.setFontSize(18);
                doc.text('Ficha de producto', 14, 20);

                // Campos
                doc.setFontSize(11);
                const fields = [
                    ['ID', String(productId)],
                    ['Nombre', name],
                    ['Descripción', description],
                    ['Categorías', categories || '—'],
                    ['Subcategorías', subcats || '—'],
                ];
                let y = 35;
                fields.forEach(([label, value]) => {
                    doc.setFont(undefined, 'bold');
                    doc.text(label + ':', 14, y);
                    doc.setFont(undefined, 'normal');
                    const lines = doc.splitTextToSize(value, 150);
                    doc.text(lines, 55, y);
                    y += 8 * lines.length;
                });

                // Fotos (si las hay)
                if (existingPhotos.length > 0) {
                    y += 4;
                    doc.setFont(undefined, 'bold');
                    doc.text('Fotos:', 14, y);
                    y += 8;

                    for (const photo of existingPhotos) {
                        try {
                            const img = await toBase64(photo.url);
                            doc.addImage(img, 'JPEG', 14, y, 60, 60);
                            y += 68;
                            if (y > 260) {
                                doc.addPage();
                                y = 20;
                            }
                        } catch (_) {}
                    }
                }

                doc.save(`producto_${productId}.pdf`);
            });

            // Helper: convierte URL de imagen a base64
            function toBase64(url) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.crossOrigin = 'anonymous';
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        canvas.width = img.naturalWidth;
                        canvas.height = img.naturalHeight;
                        canvas.getContext('2d').drawImage(img, 0, 0);
                        resolve(canvas.toDataURL('image/jpeg'));
                    };
                    img.onerror = reject;
                    img.src = url;
                });
            }
        </script>

    </div>
@endsection
