@extends('layouts.layout')

@section('content')
<div class="container page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1 class="page-title">Catálogo de Productos</h1>
        <a href="/cart" class="btn btn-primary">
            🛒 Ver carrito
        </a>
    </div>
    <p class="text-muted mb-0" style="font-size:.9rem">Selecciona los productos que quieres añadir al carrito.</p>

    <div id="products-container" class="products-grid">
        <div class="text-center py-5 w-100">
            <div class="spinner"></div>
            <p class="text-muted mt-2">Cargando productos...</p>
        </div>
    </div>



    <script>
        fetch('/api/products')
            .then(res => res.json())
            .then(products => {
                const shoppingCart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
                const container = document.getElementById('products-container');

                function buildCarousel(photos, productId) {
                    if (!photos || photos.length === 0) {
                        return `<div class="product-img-fallback">🛍️</div>`;
                    }
                    if (photos.length === 1) {
                        return `
                            <div class="carousel-wrapper">
                                <div class="carousel-track">
                                    <img src="/storage/${photos[0]}" alt="Producto" loading="lazy">
                                </div>
                            </div>`;
                    }
                    const dots = photos.map((_, i) =>
                        `<span class="carousel-dot ${i === 0 ? 'active' : ''}" data-index="${i}"></span>`
                    ).join('');
                    const imgs = photos.map(url =>
                        `<img src="/storage/${url}" alt="Producto" loading="lazy">`
                    ).join('');
                    return `
                        <div class="carousel-wrapper" id="carousel-${productId}">
                            <div class="carousel-track">${imgs}</div>
                            <button class="carousel-btn prev" aria-label="Anterior">&#8249;</button>
                            <button class="carousel-btn next" aria-label="Siguiente">&#8250;</button>
                            <div class="carousel-dots">${dots}</div>
                        </div>`;
                }

                container.innerHTML = products.map(product => {
                    const price = product.current_fee ? parseFloat(product.current_fee.price).toFixed(2) : '—';
                    const photos = Array.isArray(product.photo_url) ? product.photo_url : [];
                    return `
                    <div class="product-card">
                        ${buildCarousel(photos, product.id)}
                        <div class="product-body">
                            <a href="/products/${product.id}" class="product-name">${product.name}</a>
                            <p class="product-desc">${product.description ?? ''}</p>
                            <div class="product-price">${price}€</div>
                            <div class="product-actions">
                                <button class="btn btn-primary btn-sm addToCartBtn" data-id="${product.id}">
                                    🛒 Añadir
                                </button>
                                <input id="quantity-${product.id}" class="quantity-input d-none"
                                    type="number" min="1" value="1"/>
                                <button id="removeFromCartBtn-${product.id}"
                                    class="btn btn-outline-secondary btn-sm d-none">✕ Quitar</button>
                            </div>
                        </div>
                    </div>`;
                }).join('');

                // Init carousels
                products.forEach(product => {
                    const photos = Array.isArray(product.photo_url) ? product.photo_url : [];
                    if (photos.length <= 1) return;

                    const wrapper = document.getElementById(`carousel-${product.id}`);
                    if (!wrapper) return;

                    const track  = wrapper.querySelector('.carousel-track');
                    const dots   = wrapper.querySelectorAll('.carousel-dot');
                    const prev   = wrapper.querySelector('.prev');
                    const next   = wrapper.querySelector('.next');
                    let current  = 0;

                    function goTo(index) {
                        current = (index + photos.length) % photos.length;
                        track.style.transform = `translateX(-${current * 100}%)`;
                        dots.forEach((d, i) => d.classList.toggle('active', i === current));
                    }

                    prev.addEventListener('click', () => goTo(current - 1));
                    next.addEventListener('click', () => goTo(current + 1));
                    dots.forEach(dot => dot.addEventListener('click', () => goTo(+dot.dataset.index)));
                });

                // --- Cart logic (sin cambios) ---
                function attachCartListeners(thisProductId) {
                    const btn = document.querySelector(`.addToCartBtn[data-id="${thisProductId}"]`);
                    const removeProductBtn = document.getElementById(`removeFromCartBtn-${thisProductId}`);
                    const quantityInput = document.getElementById(`quantity-${thisProductId}`);

                    removeProductBtn.addEventListener('click', () => {
                        const index = shoppingCart.findIndex(p => p.product_id === thisProductId);
                        if (index !== -1) shoppingCart.splice(index, 1);
                        localStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));
                        removeProductBtn.classList.replace('d-block','d-none');
                        quantityInput.classList.replace('d-block','d-none');
                        btn.classList.replace('d-none','d-block');
                    });

                    quantityInput.addEventListener('change', () => {
                        const item = shoppingCart.find(p => p.product_id === thisProductId);
                        if (item) {
                            item.quantity = parseInt(quantityInput.value);
                            localStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));
                        }
                    });
                }

                shoppingCart.forEach(cartItem => {
                    const btn = document.querySelector(`.addToCartBtn[data-id="${cartItem.product_id}"]`);
                    const removeProductBtn = document.getElementById(`removeFromCartBtn-${cartItem.product_id}`);
                    const quantityInput = document.getElementById(`quantity-${cartItem.product_id}`);
                    if (btn && removeProductBtn && quantityInput) {
                        quantityInput.value = cartItem.quantity;
                        btn.classList.add('d-none');
                        removeProductBtn.classList.replace('d-none','d-block');
                        quantityInput.classList.replace('d-none','d-block');
                        attachCartListeners(cartItem.product_id);
                    }
                });

                document.querySelectorAll('.addToCartBtn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const thisProductId = btn.dataset.id;
                        shoppingCart.push({ product_id: thisProductId, quantity: 1 });
                        localStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));
                        const removeProductBtn = document.getElementById(`removeFromCartBtn-${thisProductId}`);
                        const quantityInput = document.getElementById(`quantity-${thisProductId}`);
                        btn.classList.add('d-none');
                        removeProductBtn.classList.replace('d-none','d-block');
                        quantityInput.classList.replace('d-none','d-block');
                        attachCartListeners(thisProductId);
                    });
                });
            })
            .catch(err => console.error('Error:', err));
    </script>
</div>
@endsection