@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 600px;">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/products" class="btn btn-outline-secondary btn-sm me-3">← Volver a Productos</a>
        <h1 class="page-title mb-0">Editar Tarifa</h1>
    </div>

    <div id="loader" class="text-center py-5" style="display: none;">
        <div class="spinner"></div>
        <p class="text-muted mt-2">Cargando...</p>
    </div>

    <div id="product-container" class="card p-3 mb-4 text-center bg-light border-0" style="font-size:0.95rem;">
        <p class="text-muted mb-0">Cargando producto asociado...</p>
    </div>

    <div class="card p-4 mb-4">
        <form id="feeForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Precio (€)</label>
                <input id="form-price" type="number" min="0" step="0.01" name="price" class="form-control" placeholder="0.00" required />
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Día inicial</label>
                    <input id="form-start-day" type="date" name="start_day" class="form-control" required />
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Día final</label>
                    <input id="form-end-day" type="date" name="end_day" class="form-control" required />
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">Actualizar Tarifa</button>
            </div>
        </form>
    </div>

    <div id="deleteBtnContainer" class="text-end"></div>

    <script>
        const feeId = @json($id);
        const deleteBtnContainer = document.getElementById('deleteBtnContainer');
        const productContainer = document.getElementById('product-container');
        const loader = document.getElementById('loader');

        deleteBtnContainer.innerHTML = `<button class="btn btn-outline-danger" onclick="deleteItem('fee', ${feeId})">🗑️ Borrar Tarifa</button>`;

        fetch('/api/fees/' + feeId)
            .then(res => res.json())
            .then(fee => {
                document.getElementById('form-price').value = fee.price;
                document.getElementById('form-start-day').value = fee.start_day;
                document.getElementById('form-end-day').value = fee.end_day;

                productContainer.innerHTML = `
                    <div class="text-start">
                        <p class="mb-1 text-muted fw-bold text-uppercase" style="font-size:0.8rem;">PRODUCTO ASOCIADO</p>
                        <a href="/admin/products/${fee.product.id}" class="h6 text-primary-dark fw-bold text-decoration-none">${fee.product.name}</a>
                        <p class="text-muted mt-1 mb-0">${fee.product.description ?? ''}</p>
                    </div>
                `;
            })
            .catch(err => console.error('Error:', err));

        const form = document.getElementById('feeForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            loader.style.display = 'block';
            form.style.display = 'none';

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            const response = await fetch(`/api/fees/${feeId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                alert('Tarifa actualizada!');
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
