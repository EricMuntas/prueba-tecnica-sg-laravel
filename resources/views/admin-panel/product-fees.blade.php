@extends('layouts.layout')

@section('content')
<div class="container page-wrapper">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/products" class="btn btn-outline-secondary btn-sm me-3">← Volver a Productos</a>
        <h1 class="page-title mb-0">Gestión de Tarifas</h1>
    </div>

    <div id="loader" class="text-center py-5" style="display: none;">
        <div class="spinner"></div>
    </div>

    <div class="row">
        <!-- FORM NUEVA TARIFA -->
        <div class="col-lg-4 mb-4">
            <div class="card p-4 shadow-sm h-100">
                <h5 class="fw-bold mb-3 text-primary-dark">Crear nueva tarifa</h5>
                <form id="newFeeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="price" class="form-label">Precio (€):</label>
                        <input type="number" name="price" min="0" step="0.01" class="form-control" placeholder="0.00" required />
                    </div>
                    <div class="mb-3">
                        <label for="start_day" class="form-label">Desde:</label>
                        <input type="date" name="start_day" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label for="end_day" class="form-label">Hasta:</label>
                        <input type="date" name="end_day" class="form-control" required />
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Crear Tarifa</button>
                </form>
            </div>
        </div>

        <!-- LISTA DE TARIFAS -->
        <div class="col-lg-8">
            <div class="card p-0 overflow-hidden shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Vigencia</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th class="text-end pe-4" style="width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="feesTable">
                            <tr><td colspan="4" class="text-center py-4 text-muted">Cargando tarifas...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="deleteBtnContainer" class="mt-3 text-end"></div>
        </div>
    </div>

    <script>
        const productId = @json($id);
        const feesTable = document.getElementById('feesTable');
        const deleteBtnContainer = document.getElementById('deleteBtnContainer');
        const loader = document.getElementById('loader');

        deleteBtnContainer.innerHTML = `<button class="btn btn-outline-danger btn-sm" onclick="deleteItem('product', ${productId})">🗑️ Borrar todo el producto</button>`;

        fetch('/api/products/' + productId)
            .then(res => res.json())
            .then(product => {
                const dateString = new Date().toISOString().split('T')[0];

                if(!product.fees || product.fees.length === 0) {
                    feesTable.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No hay tarifas para este producto.</td></tr>';
                    return;
                }

                feesTable.innerHTML = product.fees.map(fee => {
                    let isVigente = dateString >= fee.start_day && dateString <= fee.end_day;
                    let badgeStatus = isVigente 
                        ? '<span class="badge bg-success">Vigente</span>' 
                        : '<span class="badge bg-danger">Caducado</span>';

                    return `
                    <tr id="fee${fee.id}">
                        <td class="ps-4">
                            <div class="text-muted" style="font-size:0.85rem;">
                                ${fee.start_day} <i class="text-primary px-1">→</i> ${fee.end_day}
                            </div>
                        </td>
                        <td class="fw-bold">${parseFloat(fee.price).toFixed(2)}€</td>
                        <td>${badgeStatus}</td>
                        <td class="text-end pe-4">
                            <a href="/admin/fees/${fee.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Editar</a>
                        </td>
                    </tr>
                `;
                }).join('');
            })
            .catch(err => console.error('Error:', err));

        const form = document.getElementById('newFeeForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            loader.style.display = 'block';
            form.style.display = 'none';

            const formData = new FormData(this);
            formData.append('product_id', productId);

            const response = await fetch(`/api/fees/`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                alert('Tarifa creada.');
                window.location.reload();
            } else {
                alert('Error al crear: ' + (data.message || 'Verifica los datos.'));
                loader.style.display = 'none';
                form.style.display = 'block';
            }
        });
    </script>
</div>
@endsection
