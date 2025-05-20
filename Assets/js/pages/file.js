const btnUpload = document.querySelector('#btnUpload')
const btnNuevacarpeta = document.querySelector("#btnNuevacarpeta");
const modalFile = document.querySelector("#modalFile");
const myModal = new bootstrap.Modal(modalFile);

const modalCarpeta = document.querySelector("#modalCarpeta");
const myModal1 = new bootstrap.Modal(modalCarpeta);
const frmCarpeta = document.querySelector('#frmCarpeta');

const btnSubirArchivo = document.querySelector("#btnSubirArchivo");
const file = document.querySelector("#file");

const modalCompartir = document.querySelector("#modalCompartir");
const myModal2 = new bootstrap.Modal(modalCompartir);
const id_carpeta = document.querySelector('#id_carpeta');

const carpetas = document.querySelectorAll('.carpetas');
const btnSubir = document.querySelector('#btnSubir');

//ver archivos
const btnVer = document.querySelector('#btnVer');

//compartir archivos entre los trabajadores

const compartir = document.querySelectorAll('.compartir');
const modalUsuarios = document.querySelector("#modalUsuarios");
const myModalUser = new bootstrap.Modal(modalUsuarios);
const id_archivo = document.querySelector('#id_archivo');
const frmCompartir = document.querySelector('#frmCompartir');
const usuarios = document.querySelector('#usuarios');

document.addEventListener('DOMContentLoaded', function () {
    btnUpload.addEventListener('click', function () {
        myModal.show();
    })

    btnNuevacarpeta.addEventListener('click', function () {
        myModal.hide();
        myModal1.show();
    })

    frmCarpeta.addEventListener('submit', function (e) {
        e.preventDefault();
        if (frmCarpeta.nombre.value == '') {
            alertaPerzonalizada('warning', 'EL NOMBRE ES REQUERIDO');
        } else {
            const data = new FormData(frmCarpeta)
            const http = new XMLHttpRequest();
            const url = base_url + 'admin/crearcarpeta';
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertaPerzonalizada(res.tipo, res.mensaje);
                    if (res.tipo == 'success') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }

                }

            };

        }
    })

    //SE SUBEN ARCHIVOS
    btnSubirArchivo.addEventListener('click', function () {
        myModal.hide();
        file.click();
    })

    file.addEventListener('change', function (e) {
        console.log(e.target.files[0]);
        const data = new FormData()
        let carpeta = id_carpeta.value;
        if (!carpeta || carpeta.trim() === '') {
            carpeta = '1';
        }
        data.append('id_carpeta', id_carpeta.value)
        data.append('file', e.target.files[0])
        const http = new XMLHttpRequest();
        const url = base_url + 'admin/subirarchivos';
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                const res = JSON.parse(this.responseText);
                alertaPerzonalizada(res.tipo, res.mensaje);
                if (res.tipo == 'success') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }

            }

        };
    })

    carpetas.forEach(carpeta => {
        carpeta.addEventListener('click', function (e) {
            id_carpeta.value = e.target.id;
            myModal2.show();
        })
    });

    btnSubir.addEventListener('click', function () {
        myModal2.hide();
        file.click();
    })

    btnVer.addEventListener('click', function () {
        window.location = base_url + 'admin/ver/' + id_carpeta.value;
    })

    $(".js-states").select2({
        placeholder: 'Buscar y agregar usuarios',
        maximumSelectionLength: 5,
        minimumInputLength: 2,
        dropdownParent: $('#modalUsuarios'),
        ajax: {
            url: base_url + 'archivos/getUsuarios',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
    });

    //agregar click al enlace compartir
    compartir.forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            compartirArchivo(e.target.id);
        })
    });

    frmCompartir.addEventListener('submit', function (e) {
        e.preventDefault();
        if (id_archivo.value == '' || usuarios.value == '') {
            alertaPerzonalizada('warning', 'TODOS LOS CAMPOS SON REQUERIDOS');
        } else {
            const data = new FormData(frmCompartir)
            const http = new XMLHttpRequest();
            const url = base_url + 'archivos/compartir';
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    alertaPerzonalizada(res.tipo, res.mensaje);
                    if (res.tipo == 'success'){
                        id_archivo.value = '';
                        $('.js-states').val(null).trigger('change');
                        myModalUser.hide();
                    }
                }
            };
        }
    })
})

function compartirArchivo(id) {
    id_archivo.value = id;
    myModalUser.show();
}