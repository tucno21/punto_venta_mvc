<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/users.js',
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
                        <h5 class="m-b-10">Panel de Usuarios</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('users.dataTable') ?>">
                        <input id="urlCreate" type="hidden" data-url="<?= route('users.create') ?>">
                        <input id="urlEdit" type="hidden" data-url="<?= route('users.edit') ?>">
                        <input id="urlStatus" type="hidden" data-url="<?= route('users.status') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('users.destroy') ?>">
                        <input id="urlRoles" type="hidden" data-url="<?= route('roles.dataTable') ?>">
                    </div>
                    <div class="">
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar Usuario</button>
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
                <div class="card-header p-2">
                    <h5>Lista de usuarios</h5>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-striped" id="simpleDatatable"></table>
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
                <h5 class="modal-title h4" id="modalLabel">Formulario Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input name="name" type="text" class="form-control" id="inputName">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input name="email" type="email" class="form-control" id="inputEmail">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input name="password" type="password" class="form-control" id="inputPassword">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Rol</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-gear"></i></span>
                            <select id="inputRol" name="rol_id" class="form-select">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 text-center mt-3">
                        <input name="id" type="hidden" id="listId">
                        <button class="btn btn-primary" id="btnFormulario">Cambio</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>