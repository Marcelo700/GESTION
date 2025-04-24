const frm = document.querySelector('#formulario');
const btnNuevo = document.querySelector('#btnNuevo');
const title = document.querySelector('#title');




const modalRegistro = document.querySelector("#modalRegistro");

const myModal = new bootstrap.Modal(modalRegistro);

let tblUsuarios;

document.addEventListener('DOMContentLoaded', function () {
    //Cargar datos con datatables
    $('#tblUsuarios').DataTable({
        ajax: {
            url: base_url + 'usuarios/listar',
            dataSrc: ''
        },
        columns: [
            { data: 'acciones' },
            { data: 'id' },
            { data: 'nombres' },
            { data: 'correo' },
            { data: 'telefono' },
            { data: 'direccion' },
            { data: 'perfil' },
            { data: 'fecha' },
        ],
        language : {
            url : 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        responsive: true
    });

    btnNuevo.addEventListener('click', function () {
        title.textContent = 'NUEVO USUARIO';
        myModal.show();
    })
    //registrar usuario por AJAX
    frm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (frm.nombre.value == '' || frm.apellido.value == ''
            || frm.correo.value == '' || frm.telefono.value == ''
            || frm.direccion.value == '' || frm.clave.value == ''
            || frm.rol.value == '') {
            alertaPerzonalizada('warning', 'TODOS LOS CAMPOS SON REQUERIDOS');
        } else {
            const data = new FormData(frm)
            const http = new XMLHttpRequest();
            const url = base_url + 'usuarios/guardar';

            http.open("POST", url, true);

            http.send(data);

            http.onreadystatechange = function () {

                if (this.readyState == 4 && this.status == 200) {

                    const res = JSON.parse(this.responseText);
                    alertaPerzonalizada(res.tipo, res.mensaje);
                    if (res.tipo == 'success') {
                        frm.reset();
                        myModal.hide()
                    } else {

                    }

                }

            };

        }

    })
})
function eliminar(id) {
    const url = base_url + 'usuarios/delete/' + id;
    eliminarRegistro('ESTA SEGURO DE ELIMINAR', 'EL USUARIO NO SE ELIMINARA DE FORMA PERMANENTE', 'SI ELIMINAR', url, tblUsuarios)
    
}