const frm = document.querySelector('#formulario');
const correo = document.querySelector('#correo');
const clave = document.querySelector('#clave');


const inputReset = document.querySelector('#inputReset');
const btnProcesar = document.querySelector('#btnProcesar');
const btnreset = document.querySelector('#reset');
const myModal = new bootstrap.Modal(document.querySelector("#myModal"));

document.addEventListener('DOMContentLoaded', function () {
    frm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (correo.value == '' || clave.value == '') {
            alertaPerzonalizada('warning', 'Todos los campos con * son requeridos');
        } else {
            const data = new FormData(frm);
            const http = new XMLHttpRequest();
            const url = base_url + 'principal/validar';
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertaPerzonalizada(res.tipo, res.mensaje);
                    if (res.tipo == 'success') {
                        let timerInterval;
                        Swal.fire({
                            title: res.mensaje,
                            html: "Sera redireccionado en <b></b> milliseconds.",
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                const timer = Swal.getPopup().querySelector("b");
                                timerInterval = setInterval(() => {
                                    timer.textContent = `${Swal.getTimerLeft()}`;
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal.DismissReason.timer) {
                                window.location = base_url + 'admin';
                            }
                        })
                    }

                }

            };
        }
    })

    btnreset.addEventListener('click', function () {
        inputReset.value = '';
        myModal.show();
    })



    btnProcesar.addEventListener('click', function () {
        const correo = inputReset.value.trim();

        if (correo === '') {
            alertaPerzonalizada('error', 'Por favor, ingrese el correo electrónico');
            return; // Salir para no hacer la petición
        }

        // Opcional: Validar formato básico de correo
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            alertaPerzonalizada('error', 'Ingrese un correo electrónico válido');
            return;
        }

        // Crear solicitud HTTP
        const http = new XMLHttpRequest();
        const url = base_url + 'principal/enviarCorreo/' + inputReset.value;
        http.open("GET", url, true);

        // Opcional: Deshabilitar botón para evitar múltiples clics
        btnProcesar.disabled = true;

        http.onreadystatechange = function () {
            if (this.readyState === 4) {
                btnProcesar.disabled = false; // Rehabilitar botón

                if (this.status === 200) {
                    try {
                        const res = JSON.parse(this.responseText);
                        alertaPerzonalizada(res.tipo, res.mensaje);
                        if (res.tipo === 'success') {
                            // Aquí puedes agregar acción extra si quieres, por ejemplo limpiar input
                            inputReset.value = '';
                            myModal.hide();
                        }
                    } catch (error) {
                        alertaPerzonalizada('error', 'Error en la respuesta del servidor');
                    }
                } else {
                    alertaPerzonalizada('error', 'Error al conectar con el servidor');
                }
            }
        };

        http.send();
    });

})