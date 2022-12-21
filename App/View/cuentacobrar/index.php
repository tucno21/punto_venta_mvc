<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/cuentasCobrar.js',
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
                        <h5 class="m-b-10">Panel Creditos y Abonos</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('cuentasCobrar.dataTable') ?>">
                        <input id="urlAddAbono" type="hidden" data-url="<?= route('cuentasCobrar.abono') ?>">
                        <input id="urlReporte" type="hidden" data-url="<?= route('cuentasCobrar.reporte') ?>">
                        <input id="urlEstadoCajaUsuario" type="hidden" data-url="<?= route('cajaArqueos.estadocaja') ?>">
                    </div>
                    <div class="">
                        <!-- <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card" id="panelVentas">
                <div class="card-header p-2">
                    <h5>Lista de Ventas al crédito</h5>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-striped" id="simpleDatatable"></table>
                    </div>
                </div>
            </div>
            <div class="card p-3 d-none" id="panelCaja">
                <div class="alert alert-danger" role="alert">
                    la caja esta cerrada, debe abrir la caja para poder realizar abonos
                </div>
                <!-- botton para abrir caja -->
                <div class="text-center">
                    <a href="<?= route('cajaArqueos.index') ?>" class="btn btn-dark">Ir a Panel de Caja</a>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<div class="modal fade" id="modalInputs" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modalLabel">Cliente: </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Comprobante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-braces-asterisk"></i></span>
                            <input type="text" class="form-control" id="inputComprobante" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Fecha Abono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-braces-asterisk"></i></span>
                            <input type="date" class="form-control" id="inputFecha" value="<?= date('Y-m-d') ?>" readonly disabled>
                            <input type="hidden" id="inputFechaEnviar" value="<?= date('Y-m-d H:i:s') ?>">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Total Venta</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-fonts"></i></span>
                            <input type="number" class="form-control" id="inputTotal" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Monto Abonado</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cursor-text"></i></span>
                            <input type="number" class="form-control" id="inputMontoAbonado" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Abonar...</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cursor-text"></i></span>
                            <input type="number" class="form-control" id="inputAbonar" min="0" step="0.01">
                            <input type="hidden" id="inputVentaId">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Resta</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cursor-text"></i></span>
                            <input type="number" class="form-control" id="inputResta" readonly disabled>
                        </div>
                    </div>


                    <div class="col-md-12 text-center mt-3">
                        <input name="id" type="hidden" id="listId">
                        <button class="btn btn-primary" id="btnFormulario">Abonar Crédito</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>