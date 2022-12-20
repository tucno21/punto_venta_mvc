<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/cotizacionesindex.js',
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
                        <h5 class="m-b-10">Panel de Cotizaciones</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('cotizaciones.dataTable') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('cotizaciones.destroy') ?>">
                        <input id="urlReporte" type="hidden" data-url="<?= route('cotizaciones.reporte') ?>">
                        <input id="urlBoleta" type="hidden" data-url="<?= route('ventas.boletacoti') ?>">
                        <input id="urlFactura" type="hidden" data-url="<?= route('ventas.facturacoti') ?>">
                        <input id="urlUpdateElectronico" type="hidden" data-url="<?= route('cotizaciones.updateElectronico') ?>">
                        <input id="urlReportePdf" type="hidden" data-url="<?= route('cotizaciones.cotizacionespdf') ?>">
                        <input id="urlReporteExcel" type="hidden" data-url="<?= route('cotizaciones.cotizacionesexcel') ?>">
                        <input id="urlVentaInterna" type="hidden" data-url="<?= route('cotizaciones.ventainterna') ?>">
                    </div>
                    <div class="">
                        <a a href="<?= route('cotizaciones.create') ?>" class="btn btn-primary btn-sm">Registrar Cotización</a>
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
                            R. de Cotizaciones
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
<?php include ext('layoutdash.footer') ?>