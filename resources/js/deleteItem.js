// Guarda temporalmente qué ítem se va a borrar
let itemToDelete = { type: null, id: null };
let bsModal = null;

// 1. El botón "Borrar" de la tabla llama a esta función
window.deleteItem = function(type, id) {
    itemToDelete = { type, id };

    // Inicializa y abre el modal de Bootstrap
    const modalEl = document.getElementById('deleteModal');
    if (!bsModal) {
        bsModal = new bootstrap.Modal(modalEl);
    }
    bsModal.show();
};

// 2. El botón "Borrar" dentro del modal llama a esta función
window.confirmDelete = function() {
    const { type, id } = itemToDelete;
    if (!type || !id) return;

    // Mapea el tipo al endpoint de API correspondiente
    const endpoints = {
        product:     `/api/products/${id}`,
        category:    `/api/categories/${id}`,
        subcategory: `/api/subcategories/${id}`,
    };

    const url = endpoints[type];
    if (!url) {
        console.error('Tipo desconocido:', type);
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(url, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
        }
    })
    .then(res => {
        bsModal.hide();
        itemToDelete = { type: null, id: null };

        if (res.ok) {
            window.location.reload();
        } else {
            return res.json().then(data => {
                alert('Error al eliminar: ' + (data.message || 'Error desconocido'));
            });
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Ocurrió un error inesperado.');
        bsModal.hide();
    });
};