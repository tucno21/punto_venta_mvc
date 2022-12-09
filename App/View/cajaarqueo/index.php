<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/cajaarqueo.js',
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
                        <h5 class="m-b-10">Panel de caja Arqueo</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('cajaArqueos.dataTable') ?>">
                        <input id="urlCreate" type="hidden" data-url="<?= route('cajaArqueos.create') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('cajaArqueos.destroy') ?>">
                        <input id="urlCajas" type="hidden" data-url="<?= route('cajaArqueos.cajas') ?>">
                        <input id="urlEstadoCajaUsuario" type="hidden" data-url="<?= route('cajaArqueos.estadocaja') ?>">
                        <input id="urlReporte" type="hidden" data-url="<?= route('cajaArqueos.reporte') ?>">
                    </div>
                    <!-- <div class="">
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar Categoria</button>
                    </div> -->
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
                        <div class="col-md-6">
                            <h5>Lista de caja Arqueo</h5>
                        </div>
                        <div class="col-md-6">
                            <!-- <div class="float-end"> -->
                            <button id="btnCrear" type="button" class="btn btn-success btn-lg caja-cerrada"><i class="bi bi-lock"></i> Aperturar Caja Nueva</button>
                            <!-- </div> -->
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modalLabel">Formulario Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Selecione Caja</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-inbox"></i></span>
                            <select id="inputCajaId" class="form-select"></select>
                        </div>
                    </div>

                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Monto de Inicio</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                            <input type="number" class="form-control" id="inputMonto">
                        </div>
                    </div>

                    <div class="col-md-12 text-center mt-3">
                        <input name="id" type="hidden" id="listId">
                        <button class="btn btn-primary" id="btnFormulario">Registrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>