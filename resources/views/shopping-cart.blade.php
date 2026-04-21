@extends('layouts.layout')

@section('content')
    <x-link url="/" text="Go to dashboard"></x-link>

    <div id="cart-container">
        <p>Cargando carrito...</p>
        <x-loader></x-loader>
    </div>

    <script>
        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                const shoppingCart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                const container = document.getElementById('cart-container');

                if (shoppingCart.length === 0) {
                    container.innerHTML = `
                        <div class="empty-cart">
                            <p>Tu carrito está vacío.</p>
                            <a href="/products">Ver productos</a>
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
                    <h2>Tu carrito</h2>

                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            ${cartItems.map(item => `
                                    <tr id="row-${item.product_id}">
                                        <td>
                                            <a href="/products/${item.product_id}">${item.name}</a>
                                        </td>
                                        <td>${parseFloat(item.price).toFixed(2)}€</td>
                                        <td>
                                            <input
                                                class="quantity-input"
                                                data-id="${item.product_id}"
                                                data-price="${item.price}"
                                                type="number"
                                                min="1"
                                                value="${item.quantity}"
                                            />
                                        </td>
                                        <td id="subtotal-${item.product_id}">
                                            ${(parseFloat(item.price) * item.quantity).toFixed(2)}€
                                        </td>
                                        <td>
                                            <button class="removeBtn" data-id="${item.product_id}">Eliminar</button>
                                        </td>
                                    </tr>
                                `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total</strong></td>
                                <td colspan="2"><strong id="total-price">${total.toFixed(2)}€</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="order-date">
                        <label for="order-date">Fecha del pedido</label>
                        <input type="date" id="order-date" min="${new Date().toISOString().split('T')[0]}"/>
                    </div>

                    <button id="confirm-order">Confirmar pedido</button>
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
                        const newQty = parseInt(input.value);

                        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                        const item = cart.find(p => p.product_id == productId);
                        if (item) {
                            item.quantity = newQty;
                            localStorage.setItem('shoppingCart', JSON.stringify(cart));
                        }

                        document.getElementById(`subtotal-${productId}`).textContent =
                            (price * newQty).toFixed(2) + '€';

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
                                <div class="empty-cart">
                                    <p>Tu carrito está vacío.</p>
                                    <a href="/products">Ver productos</a>
                                </div>
                            `;
                        }
                    });
                });

                // Confirmar pedido
                document.getElementById('confirm-order').addEventListener('click', () => {
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
                        items: cart,
                    };

                    console.log('Pedido a enviar:', orderData);

                    // fetch('/api/orders', {
                    //     method: 'POST',
                    //     headers: { 'Content-Type': 'application/json' },
                    //     body: JSON.stringify(orderData),
                    // })
                    // .then(res => res.json())
                    // .then(order => {
                    //     localStorage.removeItem('shoppingCart');
                    //     window.location.href = `/orders/${order.id}`;
                    // });
                });
            })
            .catch(err => console.error('Error:', err));
    </script>
@endsection
