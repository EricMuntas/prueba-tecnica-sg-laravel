@extends('layouts.layout')

@section('content')
    <x-link url="/" text="Go to dashboard"></x-link>
    <div id="products-container">
        <p>Cargando productos...</p>
        <x-loader></x-loader>
    </div>
    <a href="/cart">
        <button type="button">Ir a la cesta</button>

    </a>

    <script>
        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                const shoppingCart = JSON.parse(localStorage.getItem('shoppingCart')) || [];

                const container = document.getElementById('products-container');
                container.innerHTML = products.map(product => `
                    <div>
                        <a href="/products/${product.id}">${product.name}</a>
                        <p>${product.description}</p>
                        <p>${product.current_fee.price}€</p>

                        <div class="productCartOptions">
                            <button class="addToCartBtn" data-id="${product.id}">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <input id="quantity-${product.id}" class="quantity-input d-none" type="number" min="1" placeholder="1" value="1"/>

                            <button id="removeFromCartBtn-${product.id}" class="d-none">X</button>
                        </div>
                    </div>
                `).join('');

                function attachCartListeners(thisProductId) {
                    const btn = document.querySelector(`.addToCartBtn[data-id="${thisProductId}"]`);
                    const removeProductBtn = document.getElementById(`removeFromCartBtn-${thisProductId}`);
                    const quantityInput = document.getElementById(`quantity-${thisProductId}`);

                    removeProductBtn.addEventListener('click', () => {
                        const index = shoppingCart.findIndex(p => p.product_id === thisProductId);
                        if (index !== -1) shoppingCart.splice(index, 1);
                        localStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));

                        removeProductBtn.classList.remove('d-block');
                        quantityInput.classList.remove('d-block');
                        removeProductBtn.classList.add('d-none');
                        quantityInput.classList.add('d-none');
                        btn.classList.remove('d-none');
                        btn.classList.add('d-block');
                    });

                    quantityInput.addEventListener('change', () => {
                        const item = shoppingCart.find(p => p.product_id === thisProductId);
                        if (item) {
                            item.quantity = parseInt(quantityInput.value);
                            localStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));
                        }
                    });
                }

                // Restaurar estado del carrito al cargar la página
                shoppingCart.forEach(cartItem => {
                    const btn = document.querySelector(`.addToCartBtn[data-id="${cartItem.product_id}"]`);
                    const removeProductBtn = document.getElementById(
                        `removeFromCartBtn-${cartItem.product_id}`);
                    const quantityInput = document.getElementById(`quantity-${cartItem.product_id}`);

                    if (btn && removeProductBtn && quantityInput) {
                        quantityInput.value = cartItem.quantity;

                        btn.classList.add('d-none');
                        removeProductBtn.classList.remove('d-none');
                        removeProductBtn.classList.add('d-block');
                        quantityInput.classList.remove('d-none');
                        quantityInput.classList.add('d-block');

                        attachCartListeners(cartItem.product_id);
                    }
                });

                // Añadir nuevos productos al carrito
                document.querySelectorAll('.addToCartBtn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const thisProductId = btn.dataset.id;

                        const thisProduct = {
                            product_id: thisProductId,
                            quantity: 1
                        };
                        shoppingCart.push(thisProduct);
                        localStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));

                        const removeProductBtn = document.getElementById(
                            `removeFromCartBtn-${thisProductId}`);
                        const quantityInput = document.getElementById(`quantity-${thisProductId}`);

                        btn.classList.add('d-none');
                        removeProductBtn.classList.remove('d-none');
                        removeProductBtn.classList.add('d-block');
                        quantityInput.classList.remove('d-none');
                        quantityInput.classList.add('d-block');

                        attachCartListeners(thisProductId);
                    });
                });
            })
            .catch(err => console.error('Error:', err));
    </script>
@endsection
