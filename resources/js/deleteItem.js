window.deleteItem = function(type, id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este elemento?')) return;

    let url = type === 'subcategory' ? `/api/subcategories/${id}` : `/api/categories/${id}`;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (res.ok) {
            alert('Elemento eliminado con éxito');
            window.location.reload();
        } else {
            alert('Hubo un error al eliminar');
        }
    })
    .catch(err => console.error('Error:', err));
}