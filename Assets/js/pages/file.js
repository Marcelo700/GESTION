const btnUpload = document.querySelector('#btnUpload')
const modalFile = document.querySelector("#modalFile");
const modalFile = document.querySelector("#modalFile");
const myModal = new bootstrap.Modal(modalFile);

const modalCarpeta = document.querySelector("#modalCarpeta");
const myModal1 = new bootstrap.Modal(modalCarpeta);

document.addEventListener('DOMContentLoaded', function(){
    btnUpload.addEventListener('click', function(){
        myModal.show();
    })
})