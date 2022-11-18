<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/factura/serieCorrelativos.js',
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
                        <h5 class="m-b-10">Panel de Serie Correlativos</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('serieCorrelativos.dataTable') ?>">
                        <input id="urlCreate" type="hidden" data-url="<?= route('serieCorrelativos.create') ?>">
                        <input id="urlEdit" type="hidden" data-url="<?= route('serieCorrelativos.edit') ?>">
                        <input id="urlStatus" type="hidden" data-url="<?= route('serieCorrelativos.status') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('serieCorrelativos.destroy') ?>">
                    </div>
                    <div class="">
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar</button>
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
                    <h5>Lista de Serie Correlativos</h5>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modalLabel">Formulario Serie - Correlativo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Tipo de Comprobante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-5-square"></i></span>
                            <input name="tipo_comprobante" type="text" class="form-control" id="inputComprobante">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Serie</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-break"></i></span>
                            <input name="serie" type="text" class="form-control" id="inputSerie">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Correlativo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-123"></i></span>
                            <input name="correlativo" type="number" class="form-control" id="inputCorrelativo">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Tipo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-app"></i></span>
                            <input name="tipo" type="text" class="form-control" id="inputTipo">
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