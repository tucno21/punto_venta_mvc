<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/permisos.js',
];
?>
<?php include ext('layoutdash.head') ?>
<div class="pcoded-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Panel de permisos</h5>
                        <input id="urlPermisos" type="hidden" data-url="<?= route('permissions.listaPermissions') ?>">
                        <input id="urlEditarPermiso" type="hidden" data-url="<?= route('permissions.edit') ?>">
                        <input id="urlEliminarPermiso" type="hidden" data-url="<?= route('permissions.destroy') ?>">
                        <input id="urlCrearPermiso" type="hidden" data-url="<?= route('permissions.create') ?>">
                    </div>
                    <div class="">
                        <?php if (can('users.create')) : ?>
                            <button id="btnCrearPermiso" type="button" class="btn btn-primary btn-sm">Crear Permiso</button>
                        <?php endif;  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de Permisos</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-striped" id="simpleDatatable"></table>
                        <!-- <div id="tableGrid"></div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<div class="modal fade" id="modalInputs" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modalLabel">Large Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="per_name" class="form-label">Permiso(nombre de la ruta)</label>
                        <input name="per_name" type="text" class="form-control" id="per_name">
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Detalle</label>
                        <input name="description" type="text" class="form-control" id="description">
                        <input name="id" type="hidden" id="listId">
                    </div>

                    <div class="col-md-12 text-center mt-3">
                        <button class="btn btn-primary" id="botonPermiso">Crear permiso</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>