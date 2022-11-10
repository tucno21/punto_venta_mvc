<?php include ext('layoutdash.head') ?>
<div class="pcoded-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Panel de permisos</h5>
                        <input id="listaPermisos" type="hidden" data-url="<?= route('permissions.listaPermissions') ?>">
                        <input id="editarPermiso" type="hidden" data-url="<?= route('permissions.edit') ?>">
                        <input id="eliminarPermiso" type="hidden" data-url="<?= route('permissions.destroy') ?>">
                    </div>
                    <div class="">
                        <?php if (can('users.create')) : ?>
                            <a href="<?= route('permissions.create') ?>" class="btn btn-outline-dark btn-sm">Crear permisos</a>
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
                    <h5>Lista de usuarios</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-striped" id="simpleDatatable">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<?php include ext('layoutdash.footer') ?>