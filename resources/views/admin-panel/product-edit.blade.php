@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 800px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="/admin/products" class="btn btn-outline-secondary btn-sm me-3">← Volver</a>
            <h1 class="page-title mb-0">Editar Producto</h1>
        </div>
        <div class="d-flex gap-2">
            <button id="export-pdf-btn" class="btn btn-outline-danger btn-sm">📄 Exportar a PDF</button>
            <a href="/admin/products/{{ $id }}/fees" class="btn btn-outline-primary btn-sm">💶 Ver Tarifas</a>
        </div>
    </div>

    <div id="loader" class="text-center py-5" style="display: none;">
        <div class="spinner"></div>
        <p class="text-muted mt-2">Guardando cambios...</p>
    </div>

    <div id="products-container" class="text-center text-muted mb-3 d-none">
        <p>Cargando producto...</p>
    </div>

    <form id="productForm" class="card p-4 shadow-sm border-0 mb-4">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input id="form-name" type="text" name="name" class="form-control" placeholder="Cargando..." required />
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <input id="form-description" type="text" name="description" class="form-control" placeholder="Cargando..." required />
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
                    <option value="">Cargando subcategorías...</option>
                </select>
                <div class="mt-2">
                    <span class="text-muted small">Subcategorías añadidas:</span>
                    <div id="added-subcategories-container" class="d-flex gap-2 flex-wrap mt-1"></div>
                </div>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="mb-4">
            <label for="photo-input" class="form-label fw-semibold">Fotos del producto (máx. 3):</label>
            <input type="file" id="photo-input" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp" multiple />
            <small id="photo-count-msg" class="text-muted mt-1 d-block"></small>
            <div id="photo-preview-container" class="d-flex gap-3 mt-3 flex-wrap"></div>
        </div>

        <p id="error-message-container" class="text-danger fw-semibold" style="font-size:0.9rem;"></p>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">Actualizar Producto</button>
        </div>
    </form>

    <div id="deleteBtnContainer" class="text-end"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const productId = @json($id);
        const deleteBtnContainer = document.getElementById('deleteBtnContainer');
        const loader = document.getElementById('loader');
        const form = document.getElementById('productForm');

        deleteBtnContainer.innerHTML = `<button class="btn btn-outline-danger" onclick="deleteItem('product', ${productId})">🗑️ Borrar Producto</button>`;

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

                document.getElementById('form-name').value = product.name;
                document.getElementById('form-description').value = product.description;

                if (Array.isArray(product.categories)) {
                    assignedCategoriesId = product.categories.map(c => String(c.id));
                }
                if (Array.isArray(product.subcategories)) {
                    assignedSubcategoriesId = product.subcategories.map(s => String(s.id));
                }

                categoriesSelect.innerHTML =
                    '<option value="">Seleccionar Categoría...</option>' +
                    categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                updateAssignedCategoriesScreen();
                updateAssignedSubcategoriesScreen();
                updateSubcategoriesDropdown();

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

        // ─── Fotos ────────────────────────────────────────────────────────
        const photoInput = document.getElementById('photo-input');
        const photoPreview = document.getElementById('photo-preview-container');
        const photoCountMsg = document.getElementById('photo-count-msg');
        const MAX_PHOTOS = 3;

        let selectedFiles = []; 
        let existingPhotos = []; 
        let deletedPhotoUrls = []; 

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
            photoCountMsg.textContent = `${total}/${MAX_PHOTOS} fotos vinculadas al producto`;
            photoInput.disabled = total >= MAX_PHOTOS;

            existingPhotos.forEach((photo, index) => {
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'position:relative;display:inline-block;';
                wrapper.innerHTML = `
                    <div class="shadow-sm rounded" style="overflow:hidden; border:2px solid #e2e8f0;">
                    <img src="/storage/${photo.url}" style="width:110px;height:110px;object-fit:cover;" />
                    </div>
                    <span data-existing-index="${index}" class="remove-existing-photo" title="Eliminar foto guardada"
                        style="position:absolute;top:-8px;right:-8px;background:#ef4444;color:#fff;
                               border-radius:50%;width:24px;height:24px;display:flex;
                               align-items:center;justify-content:center;cursor:pointer;font-size:14px; font-weight:bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">✕</span>`;
                photoPreview.append(wrapper);
            });

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position:relative;display:inline-block; opacity: 0.85;';
                    wrapper.innerHTML =
                        `
                        <div class="shadow-sm rounded" style="overflow:hidden; border:2px dashed #9ca3af;">
                        <img src="${e.target.result}" style="width:110px;height:110px;object-fit:cover;" />
                        </div>
                        <span data-index="${index}" class="remove-photo" title="Descartar nueva selección"
                            style="position:absolute;top:-8px;right:-8px;background:#6b7280;color:#fff;
                                   border-radius:50%;width:24px;height:24px;display:flex;
                                   align-items:center;justify-content:center;cursor:pointer;font-size:14px; font-weight:bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">✕</span>`;
                    photoPreview.append(wrapper);
                };
                reader.readAsDataURL(file);
            });
        };

        photoPreview.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-photo')) {
                const index = parseInt(e.target.dataset.index);
                selectedFiles.splice(index, 1);
                renderPhotoPreviews();
            }
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
                'Revisión necesaria: No puedes dejar el campo ' + field + ' vacío.';
        };

        // ─── Export PDE ───────────────────────────────────────────────────────
        document.getElementById('export-pdf-btn').addEventListener('click', async () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const name = document.getElementById('form-name').value;
            const description = document.getElementById('form-description').value;
            const categories = [...addedCategoriesContainer.querySelectorAll('.badge')]
                .map(el => el.textContent.replace('✕', '').trim()).join(', ');
            const subcats = [...addedSubcategoriesContainer.querySelectorAll('.badge')]
                .map(el => el.textContent.replace('✕', '').trim()).join(', ');

            doc.setFontSize(18);
            doc.text('Ficha de producto', 14, 20);

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

            if (existingPhotos.length > 0) {
                y += 4;
                doc.setFont(undefined, 'bold');
                doc.text('Fotos guardadas:', 14, y);
                y += 8;

                for (const photo of existingPhotos) {
                    try {
                        const img = await toBase64('/storage/' + photo.url);
                        doc.addImage(img, 'JPEG', 14, y, 60, 60);
                        y += 68;
                        if (y > 260) {
                            doc.addPage();
                            y = 20;
                        }
                    } catch (_) {}
                }
            }
            doc.save(`${name.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_ID${productId}.pdf`);
        });

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
