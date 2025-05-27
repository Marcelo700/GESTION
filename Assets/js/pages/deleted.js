let tblarchivos;

document.addEventListener('DOMContentLoaded', function () {
    //Cargar datos con datatables
    tblarchivos = $('#tblarchivos').DataTable({
        ajax: {
            url: base_url + 'archivos/listarHistorial',
            dataSrc: ''
        },
        columns: [
            { data: 'accion' },
            { data: 'id' },
            { data: 'nombre' },
            { data: 'tipo' },
            { data: 'fecha_create' },
            { data: 'elimina' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        responsive: true,
        order: [[1, 'desc']],
        
    });
})

function restaurar(id) {
    const url = base_url + 'archivos/delete/' + id;
    eliminarRegistro('ESTA SEGURO DE restaurar', 'EL ARCHIVO APARECERA EN EL MISMO DIRECTORIO', 'SI RESTAURAR', url, tblarchivos)

}