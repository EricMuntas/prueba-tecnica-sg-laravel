@extends('layouts.layout')

@section('content')
    <div>
        <x-link url="/admin" text="Go to admin panel"></x-link>
        <x-link url="/admin/products" text="go back"></x-link>

        <div id="product-container">
            <p>Cargando producto...</p>
            <x-loader></x-loader>
        </div>

        <div id="loader" style="display: none;">
            <x-loader></x-loader>
        </div>

        <form id="feeForm">
            @csrf
            <input id="form-price" type="number" min="0" step="0.01" name="price" placeholder="0" />
            <input id="form-start-day" type="date" name="start_day" />
            <input id="form-end-day" type="date" name="end_day" />


            <button type="submit">Actualizar</button>
        </form>

        <div id="deleteBtnContainer"></div>
        {{-- <x-link url="/admin/products/{{ $id }}/fees" text="Ver fees"></x-link> --}}

        <script>
            const feeId = @json($id);
            const deleteBtnContainer = document.getElementById('deleteBtnContainer');
            const productContainer = document.getElementById('product-container');

            deleteBtnContainer.innerHTML = `<button onclick="deleteItem('fee', ${feeId})">Borrar</button>`;

            // get data para presvisualizar los datos
            fetch('/api/fees/' + feeId)
                .then(res => res.json())
                .then(fee => {

                    console.log(fee);

                    let formPrice = document.getElementById('form-price');
                    formPrice.value = fee.price;

                    let formStartDay = document.getElementById('form-start-day');
                    formStartDay.value = fee.start_day;

                    let formEndDay = document.getElementById('form-end-day');
                    formEndDay.value = fee.end_day;

                    productContainer.innerHTML = `
                    <div>
                        <p>Producto asociado:</p>
                        <a href="/admin/products/${fee.product.id}">${fee.product.name}</a>
                        <p>${fee.product.description}</p>
                        </div>
                    `



                })
                .catch(err => console.error('Error:', err));



            const form = document.getElementById('feeForm');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Mostrar loader, ocultar form
                loader.style.display = 'block';
                form.style.display = 'none';

                const formData = new FormData(this);
                formData.append('_method', 'PUT'); // Laravel requiere POST + _method=PUT para leer FormData

                console.log(formData);

                const response = await fetch(`/api/fees/${feeId}`, {
                    method: 'POST', // Usamos POST aquí
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();
                console.log(data);

                if (response.ok) {
                    alert('Tarifa actualizado!');
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
