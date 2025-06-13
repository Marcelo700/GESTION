const btnUpload = document.querySelector('#btnUpload')
const btnNuevacarpeta = document.querySelector("#btnNuevacarpeta");
const myModal = new bootstrap.Modal(document.querySelector("#modalFile"));


const myModal1 = new bootstrap.Modal(document.querySelector("#modalCarpeta"));
const frmCarpeta = document.querySelector('#frmCarpeta');

const btnSubirArchivo = document.querySelector("#btnSubirArchivo");
const file = document.querySelector("#file");


const myModal2 = new bootstrap.Modal(document.querySelector("#modalCompartir"));
const id_carpeta = document.querySelector('#id_carpeta');

const carpetas = document.querySelectorAll('.carpetas');
const btnSubir = document.querySelector('#btnSubir');
const content_acordeon = document.querySelector('#accordionFlushExample');

// ELIMINAR ARCGIVOS RECIENTES
const eliminar = document.querySelectorAll('.eliminar');

//ver archivos
const btnVer = document.querySelector('#btnVer');

//compartir archivos entre los trabajadores

const compartir = document.querySelectorAll('.compartir');
const myModalUser = new bootstrap.Modal(document.querySelector("#modalUsuarios"));
const frmCompartir = document.querySelector('#frmCompartir');
const usuarios = document.querySelector('#usuarios');

const btnCompartir = document.querySelector('#btnCompartir');
const container_archivos = document.querySelector('#container_archivos');
const btnverDetalle = document.querySelector('#btnverDetalle');

const modalArchivos = new bootstrap.Modal(document.querySelector("#modalArchivos"));

// Vincular etiquetas a carpeta
let carpetaActual = null;
const btnEtiquetasCarpeta = document.querySelector('#btnEtiquetasCarpeta');
const modalEtiquetasCarpeta = document.querySelector('#modalEtiquetasCarpeta');
const selectEtiquetasCarpeta = document.querySelector('#selectEtiquetasCarpeta');
const idCarpetaEtiquetas = document.querySelector('#idCarpetaEtiquetas');

const etiquetasVinculadasContainer = document.createElement('div');
etiquetasVinculadasContainer.className = 'mb-2';
if (modalEtiquetasCarpeta) {
    // Insertar el contenedor de chips antes del select si no existe
    const form = modalEtiquetasCarpeta.querySelector('form');
    if (form && !form.querySelector('.chips-etiquetas-vinculadas')) {
        etiquetasVinculadasContainer.classList.add('chips-etiquetas-vinculadas');
        form.insertBefore(etiquetasVinculadasContainer, form.querySelector('.mb-3'));
    }
}

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
        modalArchivos.show();
    })

    

    carpetas.forEach(carpeta => {
        carpeta.addEventListener('click', function (e) {
            id_carpeta.value = e.target.id;
            myModal2.show();
        })
    });

    btnSubir.addEventListener('click', function () {
        myModal2.hide();
        modalArchivos.show();
    })

    btnVer.addEventListener('click', function () {
        window.location = base_url + 'admin/ver/' + id_carpeta.value;
    })

    $(".js-states").select2({
        theme: 'bootstrap-5',
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
        if (usuarios.value == '') {
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
                    if (res.tipo == 'success') {
                        $('.js-states').val(null).trigger('change');
                        myModalUser.hide();
                    }
                }
            };
        }
    })

    //Compartir archivos por carpeta
    btnCompartir.addEventListener('click', function () {
        verArchivos();
    })

    //Ver detalle compartido
    btnverDetalle.addEventListener('click', function () {
        window.location = base_url + 'admin/verdetalle/' + id_carpeta.value;
    })

    //eliminar archivo reciente

    eliminar.forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            let id = e.target.getAttribute('data-id');
          
            if (typeof eliminarRegistro !== 'function') {
                console.error('eliminarRegistro no está definida');
            } else {
                const url = base_url + 'archivos/eliminar/' + id;
                console.log('URL a eliminar:', url);
                eliminarRegistro('ESTA SEGURO DE ELIMINAR', 'EL ARCHIVO SE ELIMINARA DE FORMA PERMANENTE EN 30 DIAS', 'SI ELIMINAR', url, null);
            }
        })
    });

    if (btnEtiquetasCarpeta) {
        btnEtiquetasCarpeta.addEventListener('click', function () {
            idCarpetaEtiquetas.value = document.querySelector('#id_carpeta').value;
            cargarEtiquetasCarpeta();
            const modal = new bootstrap.Modal(modalEtiquetasCarpeta);
            modal.show();
        });
    }
})

//iniciar Dropzone

Dropzone.options.uploadForm = { 
  dictDefaultMessage: 'ARRASTRAR Y SOLTAR ARCHIVOS',
  dictRemoveFile: 'Eliminar' ,
  autoProcessQueue: false,
  uploadMultiple: true,
  parallelUploads: 10,
  maxFiles: 10,
  addRemoveLinks: true,

  // The setting up of the dropzone
  init: function() {
    var myDropzone = this;

    // First change the button to actually tell Dropzone to process the queue.
    document.querySelector("#btnProcesar").addEventListener("click", function(e) {
      // Make sure that the form isn't actually being sent.
      e.preventDefault();
      e.stopPropagation();
      myDropzone.processQueue();
    });
    this.on("successmultiple", function(files, response) {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
  }
 
}

//fin Dropzone

function compartirArchivo(id) {
    const http = new XMLHttpRequest();
    const url = base_url + 'archivos/buscarCarpeta/' + id;
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            console.log(this.responseText)
            id_carpeta.value = res.id_carpeta;
            content_acordeon.classList.add('d-none');
            container_archivos.innerHTML = `<input type="hidden" value="${res.id}" name="archivos[]">`
            myModalUser.show();
        }
    };
}

function verArchivos() {
    const http = new XMLHttpRequest();
    const url = base_url + 'archivos/verArchivos/' + id_carpeta.value;
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            if (res.length > 0) {
                content_acordeon.classList.remove('d-none');
                res.forEach(archivo => {
                    html += `<div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${archivo.id}" 
                        name="archivos[]" id="flexCheckDefault_${archivo.id}">
                        <label class="form-check-label" for="flexCheckDefault_${archivo.id}">
                            ${archivo.nombre}
                        </label>
                    </div>`;
                });
                // cargarDetalle(id_carpeta.value);
            } else {
                html = `<div class="alert alert-custom alert-indicator-right indicator-warning" 
                role="alert">
                <div class="alert-content">
                    <span class="alert-title">Warning!</span>
                    <span class="alert-text">CARPETA VACIA</span>
                </div>
            </div>`;

            }
            container_archivos.innerHTML = html;
            myModal2.hide();
            myModalUser.show();
        }
    };
}

function cargarEtiquetasCarpeta() {
    etiquetasVinculadasContainer.innerHTML = '<span class="text-muted">Cargando etiquetas vinculadas...</span>';
    selectEtiquetasCarpeta.innerHTML = '<option value="">Cargando etiquetas...</option>';
    fetch(base_url + 'etiquetas/getEtiquetasCarpeta/' + idCarpetaEtiquetas.value)
        .then(response => response.json())
        .then(vinculadas => {
            // Mostrar chips con botón de desvincular
            if (vinculadas.length > 0) {
                etiquetasVinculadasContainer.innerHTML = vinculadas.map(e =>
                    `<span class='badge bg-primary me-1 mb-1'>${e.nombre}
                        <button type='button' class='btn btn-sm btn-close btn-close-white ms-1' style='font-size:10px;vertical-align:middle' title='Quitar' onclick='desvincularEtiquetaCarpeta(${e.id_relacion})'></button>
                    </span>`
                ).join(' ');
            } else {
                etiquetasVinculadasContainer.innerHTML = '<span class="text-muted">Sin etiquetas vinculadas</span>';
            }
            // Obtener todas las etiquetas
            fetch(base_url + 'etiquetas/listar')
                .then(response => response.json())
                .then(etiquetas => {
                    // Filtrar solo las NO vinculadas
                    const idsVinculadas = vinculadas.map(e => e.id);
                    let options = '';
                    etiquetas.forEach(etiqueta => {
                        if (!idsVinculadas.includes(etiqueta.id)) {
                            options += `<option value="${etiqueta.id}">${etiqueta.nombre}</option>`;
                        }
                    });
                    selectEtiquetasCarpeta.innerHTML = options || '<option value="">No hay etiquetas disponibles</option>';
                });
        });
}

function desvincularEtiquetaCarpeta(id_relacion) {
    Swal.fire({
        title: '¿Quitar etiqueta?',
        text: 'Esta acción desvinculará la etiqueta de la carpeta.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(base_url + 'etiquetas/quitarEtiqueta/' + id_relacion)
                .then(response => response.json())
                .then(data => {
                    Swal.fire({ icon: data.tipo, title: 'Aviso', text: data.mensaje, timer: 1500 });
                    cargarEtiquetasCarpeta();
                });
        }
    });
}

// --- FUNCIONES GLOBALES PARA VINCULAR Y DESVINCULAR ETIQUETAS ---
function vincularEtiquetasACarpeta() {
    const idCarpeta = idCarpetaEtiquetas.value;
    const etiquetas = Array.from(selectEtiquetasCarpeta.selectedOptions).map(opt => opt.value);
    if (!idCarpeta || etiquetas.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Aviso', text: 'Selecciona al menos una etiqueta' });
        return;
    }
    let promesas = etiquetas.map(id_etiqueta => {
        const formData = new FormData();
        formData.append('id_etiqueta', id_etiqueta);
        formData.append('id_carpeta', idCarpeta);
        return fetch(base_url + 'etiquetas/asignarEtiqueta', {
            method: 'POST',
            body: formData
        }).then(response => response.json());
    });
    Promise.all(promesas).then(results => {
        let success = results.some(r => r.tipo === 'success');
        Swal.fire({ icon: success ? 'success' : 'error', title: 'Aviso', text: success ? 'Etiquetas vinculadas' : 'Error al vincular' });
        const modal = bootstrap.Modal.getInstance(modalEtiquetasCarpeta);
        modal.hide();
        cargarEtiquetasCarpeta();
    });
}

function desvincularEtiquetaCarpeta(id_relacion) {
    Swal.fire({
        title: '¿Quitar etiqueta?',
        text: 'Esta acción desvinculará la etiqueta de la carpeta.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(base_url + 'etiquetas/quitarEtiqueta/' + id_relacion)
                .then(response => response.json())
                .then(data => {
                    Swal.fire({ icon: data.tipo, title: 'Aviso', text: data.mensaje, timer: 1500 });
                    cargarEtiquetasCarpeta();
                });
        }
    });
}

window.vincularEtiquetasACarpeta = vincularEtiquetasACarpeta;
window.desvincularEtiquetaCarpeta = desvincularEtiquetaCarpeta;

// ELIMINAR ARCHIVOS (delegación de eventos)
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('eliminar')) {
        e.preventDefault();
        let id = e.target.getAttribute('data-id');
        console.log('Click eliminar', id, e.target);
        if (typeof eliminarRegistro !== 'function') {
            console.error('eliminarRegistro no está definida');
        } else {
            const url = base_url + 'archivos/eliminar/' + id;
            console.log('URL a eliminar:', url);
            eliminarRegistro('ESTA SEGURO DE ELIMINAR', 'EL ARCHIVO SE ELIMINARA DE FORMA PERMANENTE EN 30 DIAS', 'SI ELIMINAR', url, null);
        }
    }
});

// --- Lógica para editar y eliminar carpetas ---
document.addEventListener('click', function (e) {
    // Editar carpeta
    if (e.target.closest('.editar-carpeta')) {
        const btn = e.target.closest('.editar-carpeta');
        const id = btn.getAttribute('data-id');
        const nombre = btn.getAttribute('data-nombre');
        document.getElementById('editarCarpetaId').value = id;
        document.getElementById('editarCarpetaNombre').value = nombre;
        const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarCarpeta'));
        modalEditar.show();
    }
    // Eliminar carpeta
    if (e.target.closest('.eliminar-carpeta')) {
        const btn = e.target.closest('.eliminar-carpeta');
        const id = btn.getAttribute('data-id');
        Swal.fire({
            title: '¿Eliminar carpeta?',
            text: 'Solo se eliminará si está vacía.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                fetch(base_url + 'admin/eliminarCarpeta', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({ icon: data.tipo, title: 'Aviso', text: data.mensaje, timer: 1500 });
                    if (data.tipo === 'success') setTimeout(() => window.location.reload(), 1200);
                });
            }
        });
    }
});

// Guardar edición de carpeta
const frmEditarCarpeta = document.getElementById('frmEditarCarpeta');
if (frmEditarCarpeta) {
    frmEditarCarpeta.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(frmEditarCarpeta);
        fetch(base_url + 'admin/editarCarpeta', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            Swal.fire({ icon: data.tipo, title: 'Aviso', text: data.mensaje, timer: 1500 });
            if (data.tipo === 'success') setTimeout(() => window.location.reload(), 1200);
        });
    });
}