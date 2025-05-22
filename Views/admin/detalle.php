<?php include_once 'Views\template\header.php'; ?>
<div class="table-responsive">

    <div class="card">
        <div class="card-body">
            <input type="hidden" id="id_carpeta" value="<?php echo $data
            ['id_carpeta']; ?>">
            <table class="table table-striped table-hover display nowrap" style="width: 120%" id="tblDetalle">
                
                <thead>
                    <tr>
                        <th></th>
                        <th>Usuario</th>
                        <th>Archivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'Views\template\footer.php'; ?>