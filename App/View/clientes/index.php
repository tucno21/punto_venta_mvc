<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/clientes.js',
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
                        <h5 class="m-b-10">Panel de Clientes</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('clientes.dataTable') ?>">
                        <input id="urlCreate" type="hidden" data-url="<?= route('clientes.create') ?>">
                        <input id="urlEdit" type="hidden" data-url="<?= route('clientes.edit') ?>">
                        <input id="urlStatus" type="hidden" data-url="<?= route('clientes.status') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('clientes.destroy') ?>">
                        <input id="urlTipoDoc" type="hidden" data-url="<?= route('clientes.tipodocumento') ?>">
                        <input id="urlReportePdf" type="hidden" data-url="<?= route('clientes.pdf') ?>">
                        <input id="urlReporteExcel" type="hidden" data-url="<?= route('clientes.excel') ?>">
                    </div>
                    <div class="">
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar Cliente</button>
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
                    <div class="row">
                        <!-- <div class="d-flex align-items-baseline"> -->
                        <div class="col-md-2 text-center">
                            Lista de Clientes
                        </div>
                        <div class="col-md-2 text-center mt-1">
                            <button id="btnReportePdf" type="button" class="btn btn-danger btn-sm p-0 px-1">
                                <i class="bi bi-file-pdf fs-5"></i>
                            </button>
                            <button id="btnReporteExcel" type="button" class="btn btn-success btn-sm p-0 px-1">
                                <i class="bi bi-file-earmark-excel fs-5"></i>
                            </button>
                        </div>
                    </div>
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
                <h5 class="modal-title h4" id="modalLabel">Formulario Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">Tipo documento</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-input-cursor"></i></span>
                            <select id="inputTipoDoc" name="tipodoc_id" class="form-select">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">DNI/RUC</label>
                        <div class="input-group">
                            <button class="btn btn-outline-dark" type="button" id="btnBuscarCliente">Buscar</button>
                            <input name="documento" type="number" class="form-control" id="inputDocumento">
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">Pais</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-globe-americas"></i></span>
                            <input name="pais" type="text" class="form-control" id="inputPais">
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">Nombres</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input name="nombre" type="text" class="form-control" id="inputNombre">
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input name="direccion" type="text" class="form-control" id="inputDireccion">
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">Celular</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input name="telefono" type="text" class="form-control" id="inputTelefono">
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label mb-1">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input name="email" type="email" class="form-control" id="inputEmail">
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