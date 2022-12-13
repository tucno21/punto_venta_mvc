<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/password.js',
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
                        <h5 class="m-b-10">Hola: <?= session()->user()->name ?></h5>
                        <input id="urlChangePassword" type="hidden" data-url="<?= route('users.changePassword') ?>">
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
                    <h5>Cambiar Contraseña</h5>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6  mt-3">
                            <label class="form-label mb-1">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="inputPassword">
                            </div>
                        </div>
                        <div class="col-md-6  mt-3">
                            <label class="form-label mb-1">Repetir Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" class="form-control" id="inputRepeatPassword">
                            </div>
                        </div>

                        <div class="col-md-12 text-center mt-3">
                            <input type="hidden" id="inputId" value="<?= session()->user()->id ?>">
                            <button class="btn btn-primary" id="btnFormulario">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<?php include ext('layoutdash.footer') ?>