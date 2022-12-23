<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/inventarios.js',
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
                        <h5 class="m-b-10">Panel de Inventarios</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('inventarios.dataTable') ?>">
                        <input id="urlBusquedaMes" type="hidden" data-url="<?= route('inventarios.searchmonth') ?>">
                        <input id="urlInventarioMesPdf" type="hidden" data-url="<?= route('inventarios.monthpdf') ?>">
                        <input id="urlInventarioMesExcel" type="hidden" data-url="<?= route('inventarios.monthexcel') ?>">
                    </div>
                    <!-- <div class="">
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar Unidad</button>
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
                    <h5>Lista de Inventarios</h5>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <label for="mes" class="form-label">Mes Inventario</label>
                        <div class="d-flex align-items-baseline">
                            <div class="">

                                <div class="input-group input-group-sm  mb-3">
                                    <input type="month" class="form-control" aria-describedby="btnBusquedaMes" id="inputMes">
                                    <button class="btn btn-outline-success" type="button" id="btnBusquedaMes"><i class="bi bi-search"></i></button>
                                </div>
                            </div>
                            <!-- Hover added -->
                            <div class="">
                                <button type="button" class="btn btn-sm btn-danger mx-1" id="inventarioMesPDF" title="reporte en formato pdf"><i class="bi bi-file-earmark-pdf"></i></button>
                                <button type="button" class="btn btn-sm btn-success mx-1" id="inventarioMesExcel" title="reporte en formato excel"><i class="bi bi-file-earmark-excel"></i></button>
                                <!-- <button type="button" class="btn btn-sm btn-info"><i class="bi bi-gear"></i></button> -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="simpleDatatable"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<!-- <div class="modal fade" id="modalInputs" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modalLabel">Formulario Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Nombre(UNIDAD)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-unity"></i></span>
                            <input name="descripcion" type="text" class="form-control" id="inputName">
                        </div>
                    </div>
                    <div class="col-md-6  mt-3">
                        <label class="form-label mb-1">Abreviatura</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-type"></i></span>
                            <input name="codigo" type="text" class="form-control" id="inputCodigo">
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
</div> -->
<?php include ext('layoutdash.footer') ?>