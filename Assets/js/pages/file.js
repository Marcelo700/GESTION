const btnUpload = document.querySelector('#btnUpload')
const btnNuevacarpeta = document.querySelector("#btnNuevacarpeta");
const modalFile = document.querySelector("#modalFile");
const myModal = new bootstrap.Modal(modalFile);

const modalCarpeta = document.querySelector("#modalCarpeta");
const myModal1 = new bootstrap.Modal(modalCarpeta);
const frmCarpeta = document.querySelector('#frmCarpeta');

document.addEventListener('DOMContentLoaded', function(){
    btnUpload.addEventListener('click', function(){
        myModal.show();
    })

    btnNuevacarpeta.addEventListener('click', function(){
        myModal.hide();
        myModal1.show();
    })

    frmCarpeta.addEventListener('submit', function(e){
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
})