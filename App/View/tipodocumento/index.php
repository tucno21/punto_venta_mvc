<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/factura/tipodocumento.js',
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
                        <h5 class="m-b-10">Panel de Tipos de Documentos</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('tipoDocumentos.dataTable') ?>">
                        <input id="urlCreate" type="hidden" data-url="<?= route('tipoDocumentos.create') ?>">
                        <input id="urlEdit" type="hidden" data-url="<?= route('tipoDocumentos.edit') ?>">
                        <input id="urlStatus" type="hidden" data-url="<?= route('tipoDocumentos.status') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('tipoDocumentos.destroy') ?>">
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
                    <h5>Lista de Tipos de Documento</h5>
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
                <h5 class="modal-title h4" id="modalLabel">Formulario Tipo Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12  mt-3">
                        <label class="form-label mb-1">Código</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-braces-asterisk"></i></span>
                            <input name="codigo" type="text" class="form-control" id="inputCodigo">
                        </div>
                    </div>
                    <div class="col-md-12  mt-3">
                        <label class="form-label mb-1">Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-cursor-text"></i></span>
                            <input name="descripcion" type="text" class="form-control" id="inputDescripcion">
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