@extends('layouts.layout')

@section('content')
<div class="container page-wrapper" style="max-width: 900px;">
    <a href="/" class="btn btn-outline-secondary btn-sm mb-4">← Volver al dashboard</a>

    <h1 class="page-title mb-4">Tu carrito</h1>

    <div id="cart-container">
        <div class="card p-5 text-center">
            <div class="spinner"></div>
            <p class="text-muted mt-2">Cargando carrito...</p>
        </div>
    </div>

    <script>
        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                const shoppingCart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                const container = document.getElementById('cart-container');

                if (shoppingCart.length === 0) {
                    container.innerHTML = `
                        <div class="card p-5 text-center shadow-sm">
                            <div style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5f0;">🛒</div>
                            <h3 class="h5 text-primary-dark fw-bold">Tu carrito está vacío</h3>
                            <p class="text-muted mb-4">Aún no has añadido ningún producto.</p>
                            <a href="/products" class="btn btn-primary px-4 mx-auto" style="width:fit-content;">Ver productos disponibles</a>
                        </div>
                    `;
                    return;
                }

                // Cruzar carrito con productos para obtener precio y nombre
                const cartItems = shoppingCart.map(cartItem => {
                    const product = products.find(p => p.id == cartItem.product_id);
                    if (!product) return null;
                    return {
                        ...cartItem,
                        name: product.name,
                        price: product.current_fee ? product.current_fee.price : 0,
                    };
                }).filter(Boolean);

                const total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

                container.innerHTML = `
                    <div class="card shadow-sm p-4 overflow-hidden mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle cart-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio unitario</th>
                                        <th style="width: 120px;">Cantidad</th>
                                        <th>Subtotal</th>
                                        <th style="width: 100px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body">
                                    ${cartItems.map(item => `
                                        <tr id="row-${item.product_id}">
                                            <td class="fw-semibold text-primary-dark">
                                                <a href="/products/${item.product_id}" class="text-decoration-none">${item.name}</a>
                                            </td>
                                            <td class="text-muted">${parseFloat(item.price).toFixed(2)}€</td>
                                            <td>
                                                <input class="form-control form-control-sm quantity-input" 
                                                    data-id="${item.product_id}" data-price="${item.price}" 
                                                    type="number" min="1" value="${item.quantity}" />
                                            </td>
                                            <td id="subtotal-${item.product_id}" class="fw-bold">
                                                ${(parseFloat(item.price) * item.quantity).toFixed(2)}€
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger removeBtn py-1 px-2 rounded-pill" style="font-size: 0.8rem;" data-id="${item.product_id}">Quitar</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-end pe-4"><strong>Total a pagar:</strong></td>
                                        <td colspan="2"><strong id="total-price" class="text-primary fs-5">${total.toFixed(2)}€</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card shadow-sm p-4 mb-4" style="background-color: #f8f9ff; border: 1px dashed #c7d2fe;">
                        <h5 class="fw-bold text-primary-dark mb-3">Confirmación del pedido</h5>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="order-date" class="form-label mb-1">Fecha de entrega / reserva</label>
                                <input type="date" id="order-date" class="form-control" min="${new Date().toISOString().split('T')[0]}" />
                            </div>
                            <div class="col-md-6 text-md-end text-start mt-2 mt-md-0">
                                <button id="confirm-order" class="btn btn-primary px-4 py-2">✅ Confirmar y pagar</button>
                            </div>
                        </div>
                    </div>
                `;

                function recalculateTotal() {
                    const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                    const newTotal = cart.reduce((sum, cartItem) => {
                        const product = products.find(p => p.id == cartItem.product_id);
                        const price = product?.current_fee?.price || 0;
                        return sum + (price * cartItem.quantity);
                    }, 0);
                    document.getElementById('total-price').textContent = newTotal.toFixed(2) + '€';
                }

                // Listeners de cantidad
                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('change', () => {
                        const productId = input.dataset.id;
                        const price = parseFloat(input.dataset.price);
                        const newQty = parseInt(input.value) || 1;
                        if(newQty < 1) input.value = 1;

                        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                        const item = cart.find(p => p.product_id == productId);
                        if (item) {
                            item.quantity = parseInt(input.value);
                            localStorage.setItem('shoppingCart', JSON.stringify(cart));
                        }

                        document.getElementById(`subtotal-${productId}`).textContent =
                            (price * parseInt(input.value)).toFixed(2) + '€';

                        recalculateTotal();
                    });
                });

                // Listeners de eliminar
                document.querySelectorAll('.removeBtn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const productId = btn.dataset.id;

                        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                        const newCart = cart.filter(p => p.product_id != productId);
                        localStorage.setItem('shoppingCart', JSON.stringify(newCart));

                        document.getElementById(`row-${productId}`).remove();
                        recalculateTotal();

                        if (newCart.length === 0) {
                            container.innerHTML = `
                                <div class="card p-5 text-center shadow-sm">
                                    <div style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5f0;">🛒</div>
                                    <h3 class="h5 text-primary-dark fw-bold">Tu carrito está vacío</h3>
                                    <p class="text-muted mb-4">Aún no has añadido ningún producto.</p>
                                    <a href="/products" class="btn btn-primary px-4 mx-auto" style="width:fit-content;">Ver productos disponibles</a>
                                </div>
                            `;
                        }
                    });
                });

                // Confirmar pedido
                document.getElementById('confirm-order').addEventListener('click', async () => {
                    const orderDate = document.getElementById('order-date').value;

                    if (!orderDate) {
                        alert('Por favor selecciona una fecha para el pedido.');
                        return;
                    }

                    const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                    const totalCost = parseFloat(document.getElementById('total-price').textContent);
    
                    const orderData = {
                        date: orderDate,
                        cost: totalCost,
                        items: cart.map(item => ({
                            product_id: parseInt(item.product_id),
                            quantity:   parseInt(item.quantity),
                        })),
                    };

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await fetch('/api/orders', {
                            method: 'POST',
                            headers: {
                                'Content-Type':     'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN':     csrfToken,
                            },
                            body: JSON.stringify(orderData),
                        });

                        if(!res.ok) throw new Error('Error al confirmar el pedido.');
                        
                        localStorage.removeItem('shoppingCart');
                        window.location.href = `/`;
                    } catch(err) {
                        alert(err.message);
                    }
                });
            })
            .catch(err => console.error('Error:', err));
    </script>
</div>
@endsection
