@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Dashboard</h1>
            <a href="/cart" class="btn btn-primary btn-sm">🛒 Ir al carrito</a>
        </div>

        {{-- CALENDAR --}}
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div id="calendar" style="min-height: 500px;"></div>
            </div>
        </div>
    </div>

    {{-- ORDER DETAIL MODAL --}}
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Detalle del pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Fecha:</strong> <span id="modal-date"></span></p>
                    <p><strong>Coste total:</strong> <span id="modal-cost"></span></p>
                    <h6 class="mt-3">Productos</h6>
                    <ul id="modal-products" class="list-group list-group-flush"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a id="modal-edit-btn" href="#" class="btn btn-primary">✏️ Editar pedido</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));

            // ── Listen for the custom event emitted by app.js ────────────────
            document.addEventListener('order:selected', ({
                detail
            }) => {
                document.getElementById('modal-date').textContent = detail.date;
                document.getElementById('modal-cost').textContent =
                    parseFloat(detail.cost).toFixed(2) + '€';

                const productList = document.getElementById('modal-products');
                productList.innerHTML = '';

                if (detail.products.length === 0) {
                    productList.innerHTML =
                        '<li class="list-group-item text-muted">Sin productos registrados</li>';
                } else {
                    detail.products.forEach(p => {
                        const price = p.current_fee ? parseFloat(p.current_fee.price) : 0;
                        const qty = p.pivot?.quantity ?? 1;
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between';
                        li.innerHTML = `
                            <span>${p.name} × ${qty}</span>
                            <span class="text-muted">${(price * qty).toFixed(2)}€</span>
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
