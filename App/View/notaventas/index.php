<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/notaventasindex.js',
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
                        <h5 class="m-b-10">Panel de Ventas Internas</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('notaventas.dataTable') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('notaventas.destroy') ?>">
                        <input id="urlReporte" type="hidden" data-url="<?= route('notaventas.reporte') ?>">
                        <input id="urlBoleta" type="hidden" data-url="<?= route('ventas.boleta') ?>">
                        <input id="urlFactura" type="hidden" data-url="<?= route('ventas.factura') ?>">
                        <input id="urlUpdateElectronico" type="hidden" data-url="<?= route('notaventas.updateElectronico') ?>">
                        <input id="urlReportePdf" type="hidden" data-url="<?= route('notaventas.ventaspdf') ?>">
                        <input id="urlReporteExcel" type="hidden" data-url="<?= route('notaventas.ventasexcel') ?>">
                    </div>
                    <div class="">
                        <a a href="<?= route('notaventas.create') ?>" class="btn btn-primary btn-sm">Registrar Venta</a>
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
                            Reporte de ventas
                        </div>
                        <div class="col-md-6 d-flex align-items-baseline gap-2">
                            <div class="input-group">
                                <span class="input-group-text">I</span>
                                <input id="inputFechaInicio" type="date" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">F</span>
                                <input id="inputFechaFin" type="date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 text-center mt-1">
                            <button id="btnReportePdf" type="button" class="btn btn-danger btn-sm p-0 px-1" title="reporte en formato pdf">
                                <i class="bi bi-file-pdf fs-5"></i>
                            </button>
                            <button id="btnReporteExcel" type="button" class="btn btn-success btn-sm p-0 px-1" title="reporte en formato excel">
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
<?php include ext('layoutdash.footer') ?>