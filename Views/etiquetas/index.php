<?php require_once 'Views/template/header.php'; ?>
<div class="app-content">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Etiquetas</h3>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary float-end" type="button" data-bs-toggle="modal" data-bs-target="#modalEtiqueta">
                        <i class="fas fa-plus"></i> Nueva Etiqueta
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tblEtiquetas">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Color</th>
                                            <th>Acciones</th>
                                            <th>Vincular</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Etiqueta -->
<div class="modal fade" id="modalEtiqueta" tabindex="-1" aria-labelledby="modalEtiquetaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEtiquetaLabel">Nueva Etiqueta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmEtiqueta">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la etiqueta">
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="#563d7c">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="registrarEtiqueta()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Vincular Etiqueta a Carpeta -->
<div class="modal fade" id="modalVincular" tabindex="-1" aria-labelledby="modalVincularLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVincularLabel">Vincular Etiqueta a Carpeta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmVincular">
                    <input type="hidden" id="idEtiquetaVincular" name="idEtiquetaVincular">
                    <div class="mb-3">
                        <label for="selectCarpeta" class="form-label">Selecciona una carpeta</label>
                        <select class="form-select" id="selectCarpeta" name="selectCarpeta">
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="vincularEtiquetaCarpeta()">Vincular</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'Views/template/footer.php'; ?> 