<div id="modalFile" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="title-file" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-file">SUBIR O CREAR CARPETA</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid">
                    <button type="button" id="btnNuevacarpeta" class="btn btn-outline-primary m-r-xs" aria-label="Crear nueva carpeta">
                        <i class="material-icons">folder</i> Nueva Carpeta
                    </button>
                    <hr>
                    <input type="file" id="file" class="d-none" name="file" />
                    <button type="button" id="btnSubirArchivo" class="btn btn-outline-success m-r-xs" aria-label="Subir archivo">
                        <i class="material-icons">folder_zip</i> Subir Archivo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="modalCarpeta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-carpeta">NUEVA CARPETA</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmCarpeta" autocomplete="off">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="material-icons">folder</i>
                        </span>
                        <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Nombre">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalCompartir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-compartir"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_carpeta">
                <div class="d-grid">
                    <a href="#" id="btnVer" class="btn btn-outline-info m-r-xs"><i class="material-icons">folder_zip</i>ver</a>
                    <hr>
                    <button type="button" id="btnSubir" class="btn btn-outline-primary m-r-xs"><i class="material-icons">folder_zip</i>Subir archivo</button>
                    <hr>
                    <button type="button" id="btnCompartir" class="btn btn-outline-success m-r-xs"><i class="material-icons">share</i>Compartir</button>
                    <button type="button" id="btnEtiquetasCarpeta" class="btn btn-outline-warning m-r-xs"><i class="material-icons">local_offer</i> Etiquetas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalUsuarios" class="modal fade" tabindex="-1" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-usuarios">Agregar usuarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmCompartir">
                <div class="modal-body">
                    <select class="js-states form-control" id="usuarios" name="usuarios[]"
                        tabindex="-1" style="display: none; width: 100%;" multiple="multiple">
                    </select>
                    <hr>
                    <div class="accordion accordion-flush mb-3" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    SELECCIONAR ARCHIVOS A COMPARTIR
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div id="container_archivos">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a class="btn btn-outline-info" href="#" id="btnverDetalle">VER DETALLES</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline-primary">Compartir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Etiquetas para Carpeta -->
<div id="modalEtiquetasCarpeta" class="modal fade" tabindex="-1" aria-labelledby="modal-etiquetas-carpeta-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-etiquetas-carpeta-label">Vincular Etiquetas a Carpeta</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmEtiquetasCarpeta">
                    <input type="hidden" id="idCarpetaEtiquetas">
                    <div class="mb-3">
                        <label for="selectEtiquetasCarpeta" class="form-label">Selecciona etiquetas</label>
                        <select class="form-select" id="selectEtiquetasCarpeta" name="etiquetas[]" multiple>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="vincularEtiquetasACarpeta()">Vincular</button>
            </div>
        </div>
    </div>
</div>