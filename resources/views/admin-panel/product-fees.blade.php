@extends('layouts.layout')

@section('content')
    <div>
        <x-link url="/admin" text="Go to admin panel"></x-link>
        <x-link url="/admin/products" text="go back"></x-link>

        <div id="loader" style="display: none;">
            <x-loader></x-loader>
        </div>

        <form id="newFeeForm">
            <div>
                <span>Tarifa inicial:</span>
                <div>
                    <label for="start_day">Dia inicial:</label>
                    <input type="date" name="start_day" />
                </div>
                <div>
                    <label for="end_day">Dia final:</label>
                    <input type="date" name="end_day" />
                </div>
                <div>
                    <label for="price">Precio:</label>
                    <input type="number" name="price" min="0" placeholder="0" />
                </div>
            </div>
            <button type="submit">Crear</button>
        </form>
        <br>
        <div id="feesTable">

        </div>
        <div id="deleteBtnContainer"></div>


        <script>
            const productId = @json($id);
            const feesTable = document.getElementById('feesTable');
            const deleteBtnContainer = document.getElementById('deleteBtnContainer');

            deleteBtnContainer.innerHTML = `<button onclick="deleteItem('product', ${productId})">Borrar</button>`;

            // get data para presvisualizar los datos
            fetch('/api/products/' + productId)
                .then(res => res.json())
                .then(product => {
                    console.log(product);
                    const dateString = new Date().toISOString().split('T')[0]; // Obtener 'YYYY-MM-DD'

                    feesTable.innerHTML = product.fees.map(fee => {
                        let isVigente = dateString >= fee.start_day && dateString <= fee.end_day;
                        let badgeStatus = isVigente ? '<span style="color: green;">Vigente</span>' : '<span style="color: red;">Caducado</span>';

                        return `
                <div id="fee${fee.id}">
                    <span>Fee ${fee.product_id}:</span>
                   <span>${fee.start_day}</span>
                   <span>${fee.end_day}</span>
                    <p>${fee.price}€</p>
                    <div class="badge" data-id="${fee.id}">
                            ${badgeStatus}
                    </div>
                    <a href="/admin/fees/${fee.id}">
                          
         <svg class="editBtn" data-id="${fee.id}" viewBox="0 0 24 24">
                     <g>
                         <path fill="none" d="M0 0h24v24H0z"/>
                         <path d="M15.728 9.686l-1.414-1.414L5 17.586V19h1.414l9.314-9.314zm1.414-1.414l1.414-1.414-1.414-1.414-1.414 1.414 1.414 1.414zM7.242 21H3v-4.243L16.435 3.322a1 1 0 0 1 1.414 0l2.829 2.829a1 1 0 0 1 0 1.414L7.243 21z"/>
                     </g>
                 </svg></a>
                </div>
            `;
                    }).join('');

                    let badgeDiv = document.getElementById(`badge${product.fee}`)

                    // document.querySelectorAll('.editBtn').forEach(btn => {
                    //     btn.addEventListener('click', function() {
                    //         const feeId = this.dataset.id;
                    //         console.log("Editar fee:", feeId);
                    //         let feeContainer = document.getElementById(`fee${feeId}`);
                    //         feeContainer.classList.add('d-none');
                    //     });
                    // });
                })
                .catch(err => console.error('Error:', err));



            const form = document.getElementById('newFeeForm');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Mostrar loader, ocultar form
                loader.style.display = 'block';
                form.style.display = 'none';

                const formData = new FormData(this);
                formData.append('product_id', productId);

                console.log(formData);

                const response = await fetch(`/api/fees/`, {
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
                    alert('Tarifa creada.');
                    window.location.href = `/admin/products/${productId}`;
                } else {
                    alert('Error al actualizar: ' + (data.message || 'Verifica los datos.'));
                }

                loader.style.display = 'none';
                form.style.display = 'block';
            });


        </script>

    </div>
@endsection
