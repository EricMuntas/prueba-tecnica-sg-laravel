@extends('layouts.layout')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <a href="/" class="btn btn-outline-secondary btn-sm mb-3">← Volver al dashboard</a>
    <h1 class="h3 mb-4">Editar pedido #{{ $orderId }}</h1>

    {{-- Alert zone --}}
    <div id="alert-zone"></div>

    {{-- Order form --}}
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted mb-4" id="order-meta">Cargando pedido...</p>

            <form id="edit-form">
                @csrf
                <div class="mb-3">
                    <label for="order-date" class="form-label fw-semibold">Fecha del pedido</label>
                    <input type="date" id="order-date" name="date" class="form-control" required>
                </div>

                <h5 class="mt-4 mb-2">Productos</h5>
                <table class="table table-bordered align-middle" id="products-table">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th style="width:120px">Cantidad</th>
                            <th style="width:110px">Subtotal</th>
                            <th style="width:70px"></th>
                        </tr>
                    </thead>
                    <tbody id="products-body">
                        <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2"><strong>Total</strong></td>
                            <td colspan="2"><strong id="total-display">—</strong></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="/" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(async () => {
    const orderId = {{ $orderId }};
    const currentUserId = {{ Auth::id() ?? 'null' }};

    function showAlert(msg, type = 'danger') {
        document.getElementById('alert-zone').innerHTML =
            `<div class="alert alert-${type} alert-dismissible">
                ${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
             </div>`;
    }

    // ── Fetch order ─────────────────────────────────────────────────
    let order;
    try {
        const res = await fetch(`/api/orders/${orderId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (res.status === 403) {
            showAlert('No tienes permiso para editar este pedido.');
            document.getElementById('edit-form').style.display = 'none';
            return;
        }
        if (!res.ok) throw new Error('Error al cargar el pedido.');

        order = await res.json();
    } catch (e) {
        showAlert(e.message);
        return;
    }

    // ── Check ownership ──────────────────────────────────────────────
    if (order.user_id !== currentUserId) {
        showAlert('Solo puedes editar tus propios pedidos.');
        document.getElementById('edit-form').style.display = 'none';
        return;
    }

    // ── Populate form ────────────────────────────────────────────────
    document.getElementById('order-meta').textContent =
        `Pedido creado el ${order.created_at?.split('T')[0] ?? '—'}`;
    document.getElementById('order-date').value = order.date;

    renderProducts(order.products);

    // ── Products table ───────────────────────────────────────────────
    function getItems() {
        return [...document.querySelectorAll('#products-body tr[data-id]')].map(row => ({
            product_id: parseInt(row.dataset.id),
            quantity:   parseInt(row.querySelector('.qty-input').value),
            price:      parseFloat(row.dataset.price),
        }));
    }

    function recalcTotal() {
        const total = getItems().reduce((s, i) => s + i.price * i.quantity, 0);
        document.getElementById('total-display').textContent = total.toFixed(2) + '€';
    }

    function renderProducts(products) {
        const tbody = document.getElementById('products-body');
        if (!products || products.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-muted text-center">Sin productos</td></tr>';
            document.getElementById('total-display').textContent = '0.00€';
            return;
        }

        tbody.innerHTML = products.map(p => {
            const price = p.current_fee ? parseFloat(p.current_fee.price) : 0;
            const qty   = p.pivot?.quantity ?? 1;
            return `
            <tr data-id="${p.id}" data-price="${price}">
                <td><a href="/products/${p.id}">${p.name}</a></td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-input"
                        min="1" value="${qty}">
                </td>
                <td class="subtotal-cell">${(price * qty).toFixed(2)}€</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-btn">✕</button>
                </td>
            </tr>`;
        }).join('');

        // Quantity change
        tbody.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', () => {
                const row = input.closest('tr');
                const price = parseFloat(row.dataset.price);
                const qty   = parseInt(input.value) || 1;
                row.querySelector('.subtotal-cell').textContent = (price * qty).toFixed(2) + '€';
                recalcTotal();
            });
        });

        // Remove row
        tbody.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('tr').remove();
                recalcTotal();
            });
        });

        recalcTotal();
    }

    // ── Submit ────────────────────────────────────────────────────────
    document.getElementById('edit-form').addEventListener('submit', async e => {
        e.preventDefault();

        const items = getItems();
        const cost  = items.reduce((s, i) => s + i.price * i.quantity, 0);

        const payload = {
            date:  document.getElementById('order-date').value,
            cost:  cost,
            items: items.map(i => ({ product_id: i.product_id, quantity: i.quantity })),
        };

        try {
            const res = await fetch(`/api/orders/${orderId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type':     'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message ?? 'Error al guardar.');
            }

            showAlert('✓ Pedido actualizado correctamente.', 'success');
            setTimeout(() => window.location.href = '/', 1500);
        } catch (err) {
            showAlert(err.message);
        }
    });
})();
</script>
@endsection
