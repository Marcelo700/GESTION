<?php include_once 'Views\template\header.php'; ?>

<div class="app-content">
    <?php include_once 'Views\components\menu.php'; ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="section-description">
                <h1><?php echo $data['title']; ?></h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover display nowrap" style="width:120%" id="tblarchivos">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>se elimina</th>
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

    <?php
    include_once 'Views\template\footer.php';
    ?>