const frm = document.querySelector('#formulario');
const btnNuevo = document.querySelector('#btnNuevo');
const title = document.querySelector('#title');




const modalRegistro = document.querySelector("#modalRegistro");

const myModal = new bootstrap.Modal(modalRegistro);

document.addEventListener('DOMContentLoaded', function () {
    btnNuevo.addEventListener('click', function () {
        title.textContent = 'NUEVO USUARIO';
        myModal.show();
    })
    //registrar usuario 
    frm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (frm.nombre.value == '' || frm.apellido.value == ''
            || frm.correo.value == '' || frm.telefono.value == ''
            || frm.direccion.value == '' || frm.clave.value == ''
            || frm.rol.value == '') {
            alertaPerzonalizada('warning', 'TODOS LOS CAMPOS SON REQUERIDOS');
        } else {
            const data = new FormData(fmr)
            const http = new XMLHttpRequest();
            const ruta = base_url + 'usuarios/guardar';
            
            http.open("POST", url, true);

            http.send(data);

            http.onreadystatechange = function () {

                if (this.readyState == 4 && this.status == 200) {

                    const res = JSON.parse (this.responseText);
                    alertaPerzonalizada(res.tipo, res.mensaje);

                }

            };


        }

    })
})