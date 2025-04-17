const frm = document.querySelector('#formulario');
const btnNuevo = document.querySelector('#btnNuevo');

const modalRegistro = document.querySelector("#modalRegistro");

const myModal = new bootstrap.Modal(modalRegistro);

document.addEventListener('DOMContentLoaded', function(){
    btnNuevo.addEventListener('click', function(){
        myModal.show();
    })
})