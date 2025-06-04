let tblEtiquetas;

const formulario = document.querySelector('#frmEtiqueta');
const btnAccion = document.querySelector('#btnAccion');
const btnNuevo = document.querySelector('#btnNuevo');

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    tblEtiquetas = new DataTable('#tblEtiquetas', {
        ajax: {
            url: base_url + 'etiquetas/listar',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { 
                data: 'color',
                render: function(data) {
                    return `<span class="badge" style="background-color: ${data}">${data}</span>`;
                }
            },
            { data: 'acciones' },
            { data: 'vincular' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        responsive: true,
        order: [[0, 'desc']]
    });
});

// Función para registrar o modificar etiqueta
function registrarEtiqueta() {
    const url = base_url + 'etiquetas/registrar';
    const frm = document.querySelector('#frmEtiqueta');
    const formData = new FormData(frm);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.tipo === 'success') {
            const modal = document.querySelector('#modalEtiqueta');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
            frm.reset();
            tblEtiquetas.ajax.reload();
        }
        Swal.fire({
            icon: data.tipo,
            title: 'Aviso',
            text: data.mensaje,
            timer: 1500
        });
    })
    .catch(error => console.log(error));
}

// Función para editar etiqueta
function editarEtiqueta(id) {
    const url = base_url + 'etiquetas/editar/' + id;
    fetch(url)
    .then(response => response.json())
    .then(data => {
        document.querySelector('#id').value = data.id;
        document.querySelector('#nombre').value = data.nombre;
        document.querySelector('#color').value = data.color;
        document.querySelector('#modalEtiquetaLabel').textContent = 'Modificar Etiqueta';
        const modal = new bootstrap.Modal(document.querySelector('#modalEtiqueta'));
        modal.show();
    })
    .catch(error => console.log(error));
}

// Función para eliminar etiqueta
function eliminarEtiqueta(id) {
    Swal.fire({
        title: '¿Está seguro de eliminar?',
        text: '¡No podrás revertir esto!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + 'etiquetas/eliminar/' + id;
            fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.tipo === 'success') {
                    tblEtiquetas.ajax.reload();
                }
                Swal.fire({
                    icon: data.tipo,
                    title: 'Aviso',
                    text: data.mensaje,
                    timer: 1500
                });
            })
            .catch(error => console.log(error));
        }
    });
}

// Función para asignar etiqueta a archivo o carpeta
function asignarEtiqueta(id_etiqueta, id_archivo = null, id_carpeta = null) {
    const url = base_url + 'etiquetas/asignarEtiqueta';
    const formData = new FormData();
    formData.append('id_etiqueta', id_etiqueta);
    if (id_archivo) formData.append('id_archivo', id_archivo);
    if (id_carpeta) formData.append('id_carpeta', id_carpeta);

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            icon: data.tipo,
            title: 'Aviso',
            text: data.mensaje,
            timer: 1500
        });
    })
    .catch(error => console.log(error));
}

// Función para quitar etiqueta
function quitarEtiqueta(id_relacion) {
    Swal.fire({
        title: '¿Está seguro de quitar esta etiqueta?',
        text: '¡No podrás revertir esto!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + 'etiquetas/quitarEtiqueta/' + id_relacion;
            fetch(url)
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.tipo,
                    title: 'Aviso',
                    text: data.mensaje,
                    timer: 1500
                });
            })
            .catch(error => console.log(error));
        }
    });
}

// Función para obtener etiquetas de un archivo
function getEtiquetasArchivo(id_archivo) {
    const url = base_url + 'etiquetas/getEtiquetasArchivo/' + id_archivo;
    fetch(url)
    .then(response => response.json())
    .then(data => {
        // Aquí puedes manejar las etiquetas del archivo
        console.log(data);
    })
    .catch(error => console.log(error));
}

// Función para obtener etiquetas de una carpeta
function getEtiquetasCarpeta(id_carpeta) {
    const url = base_url + 'etiquetas/getEtiquetasCarpeta/' + id_carpeta;
    fetch(url)
    .then(response => response.json())
    .then(data => {
        // Aquí puedes manejar las etiquetas de la carpeta
        console.log(data);
    })
    .catch(error => console.log(error));
}

// Función para abrir el modal de vinculación
function abrirModalVincular(idEtiqueta) {
    document.querySelector('#idEtiquetaVincular').value = idEtiqueta;
    const selectCarpeta = document.querySelector('#selectCarpeta');
    selectCarpeta.innerHTML = '<option value="">Cargando carpetas...</option>';
    fetch(base_url + 'etiquetas/getCarpetas')
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Selecciona una carpeta</option>';
            data.forEach(carpeta => {
                options += `<option value="${carpeta.id}">${carpeta.nombre}</option>`;
            });
            selectCarpeta.innerHTML = options;
        });
    const modal = new bootstrap.Modal(document.querySelector('#modalVincular'));
    modal.show();
}

// Función para vincular etiqueta a carpeta
function vincularEtiquetaCarpeta() {
    const idEtiqueta = document.querySelector('#idEtiquetaVincular').value;
    const idCarpeta = document.querySelector('#selectCarpeta').value;
    if (!idCarpeta) {
        Swal.fire({ icon: 'warning', title: 'Aviso', text: 'Selecciona una carpeta' });
        return;
    }
    const formData = new FormData();
    formData.append('id_etiqueta', idEtiqueta);
    formData.append('id_carpeta', idCarpeta);
    fetch(base_url + 'etiquetas/asignarEtiqueta', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({ icon: data.tipo, title: 'Aviso', text: data.mensaje, timer: 1500 });
        if (data.tipo === 'success') {
            const modal = document.querySelector('#modalVincular');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        }
    });
} 