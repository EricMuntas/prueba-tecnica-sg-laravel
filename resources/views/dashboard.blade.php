@extends('layouts.layout')

@section('content')
<div class="container page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Mi Dashboard</h1>
        <a href="/cart" class="btn btn-outline-primary">🛒 Ver carrito</a>
    </div>

    {{-- CALENDAR --}}
    <div class="card p-3 p-md-4">
        <div id="calendar" style="min-height: 600px;"></div>
    </div>
</div>

{{-- ORDER DETAIL MODAL --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">🧾 Detalle del pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between mb-3 p-3 rounded" style="background:#f8f9ff; border:1px solid #eef2ff;">
                    <div>
                        <div class="text-muted" style="font-size:0.8rem; text-transform:uppercase; font-weight:700;">Fecha de entrega</div>
                        <div id="modal-date" class="fw-bold" style="font-size:1.05rem;"></div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted" style="font-size:0.8rem; text-transform:uppercase; font-weight:700;">Total</div>
                        <div id="modal-cost" class="fw-bold text-primary" style="font-size:1.2rem;"></div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 text-primary-dark">Contenido del pedido</h6>
                <div class="card shadow-none border">
                    <ul id="modal-products" class="list-group list-group-flush mb-0"></ul>
                </div>
            </div>
            <div class="modal-footer" style="background:#fcfdff;">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                <a id="modal-edit-btn" href="#" class="btn btn-primary px-4">✏️ Modificar</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));

        // ── Listen for the custom event emitted by app.js ────────────────
        document.addEventListener('order:selected', ({ detail }) => {
            // format date neatly
            const dateObj = new Date(detail.date);
            const dateStr = dateObj.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
            
            document.getElementById('modal-date').textContent = dateStr;
            document.getElementById('modal-cost').textContent = parseFloat(detail.cost).toFixed(2) + '€';

            const productList = document.getElementById('modal-products');
            productList.innerHTML = '';

            if (detail.products.length === 0) {
                productList.innerHTML = '<li class="list-group-item text-muted py-3 text-center">Sin productos registrados</li>';
            } else {
                detail.products.forEach(p => {
                    const price = p.current_fee ? parseFloat(p.current_fee.price) : 0;
                    const qty = p.pivot?.quantity ?? 1;
                    const subtotal = (price * qty).toFixed(2);
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center py-3';
                    li.innerHTML = `
                        <div>
                            <span class="fw-semibold d-block" style="color:#1e1b4b;">${p.name}</span>
                            <span class="text-muted" style="font-size:0.85rem;">${qty} unidad${qty>1?'es':''}</span>
                        </div>
                        <span class="fw-semibold">${subtotal}€</span>
                    `;
                    productList.appendChild(li);
                });
            }

            document.getElementById('modal-edit-btn').href = `/orders/${detail.id}`;
            orderModal.show();
        });
    });
</script>
@endsection
