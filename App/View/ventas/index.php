<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/ventasindex.js',
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
                        <h5 class="m-b-10">Panel de Ventas</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('ventas.dataTable') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('notaCDs.destroy') ?>">
                        <input id="urlReporte" type="hidden" data-url="<?= route('ventas.reporte') ?>">
                        <input id="urlEnviarSunat" type="hidden" data-url="<?= route('ventas.enviarSunat') ?>">
                        <input id="urlDownloadXml" type="hidden" data-url="<?= route('ventas.downloadxml') ?>">
                        <input id="urlDownloadCdr" type="hidden" data-url="<?= route('ventas.downloadcdr') ?>">
                        <input id="urlNotasCD" type="hidden" data-url="<?= route('notaCDs.create') ?>">
                        <input id="urlIndexNotas" type="hidden" data-url="<?= route('notaCDs.index') ?>">
                    </div>
                    <div class="">
                        <a a href="<?= route('ventas.create') ?>" class="btn btn-primary btn-sm">Registrar Venta</a>
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
                    <h5>Lista de Ventas</h5>
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