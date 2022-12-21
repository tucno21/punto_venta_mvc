<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/documentosSunat.js',
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
                        <h5 class="m-b-10">Descarga Documentos Sunat</h5>
                        <input id="urlXml" type="hidden" data-url="<?= route('documentosSunat.xml') ?>">
                        <input id="urlCdr" type="hidden" data-url="<?= route('documentosSunat.cdr') ?>">
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
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-filetype-xml f-40"></i>
                    <h4 class="m-t-20"><span class="text-success">XML</span> Sunat</h4>
                    <p class="m-b-20">Descargar Todos los Archivos XML enviados a Sunat</p>
                    <button class="btn btn-success btn-sm btn-round" id="btnXML">Descargar</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark f-40"></i>
                    <h4 class="m-t-20"><span class="text-danger">CDR</span> Sunat</h4>
                    <p class="m-b-20">Descargar todos los CDR recividos de la Sunat</p>
                    <button class="btn btn-danger btn-sm btn-round" id="btnCDR">Descargar</button>
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
                <h5 class="modal-title h4" id="modalLabel">Formulario Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12  mt-3">
                        <label class="form-label mb-1">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-bookmarks"></i></span>
                            <input name="nombre" type="text" class="form-control" id="inputName">
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