<?php
$linksCss2 = [
    // base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
    base_url . '/assets/css/listaBusqueda.css',
];

$linksScript2 = [
    base_url . '/assets/js/kardex.js',
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
                        <h5 class="m-b-10">Panel Kardex</h5>
                        <input id="urlProductokardex" type="hidden" data-url="<?= route('inventarios.tablekardex') ?>">
                        <input id="urlBuscarFechas" type="hidden" data-url="<?= route('inventarios.searchdate') ?>">
                        <input id="urlKardexPDF" type="hidden" data-url="<?= route('inventarios.kardexpdf') ?>">
                        <input id="urlKardexExcel" type="hidden" data-url="<?= route('inventarios.kardexexcel') ?>">
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
                    <h5>Busqueda por producto</h5>
                    <input type="hidden" id="productoID">
                </div>
                <div class="card-body p-2">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="btn-group btn-group-togle btn-group-sm">
                                <label class="btn btn-warning">
                                    <input id="checkedBarcode" class="form-check-input checkCompra" type="radio" name="opcion" checked="" data-link="<?= route('productos.barcodekardex') ?>"><i class="bi bi-upc"></i> Barcode
                                </label>
                                <label class="btn btn-success">
                                    <input id="checkedNombre" class="form-check-input checkCompra" type="radio" name="opcion" data-link="<?= route('productos.inputSearchkardex') ?>"><i class="bi bi-list-task"></i> Nombre
                                </label>
                            </div>
                        </div>
                        <div class="col-12 position-relative" id="grupoBarcode">
                            <!-- <label class="form-label mb-1">Buscar</label> -->
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input id="inputBuscarBarcode" type="text" class="form-control" value="" placeholder="Buscar Barcode - enter">
                            </div>
                        </div>
                        <div class="col-12 position-relative d-none" id="grupoNombre">
                            <!-- <label class="form-label mb-1">Buscar</label> -->
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input id="inputBuscarNombre" type="text" class="form-control" value="" placeholder="Buscar Nombre">
                            </div>
                        </div>
                        <!-- seleccion entre dos fechas -->
                        <div class="col-12 mt-2">
                            <div class="d-flex align-items-baseline">
                                <div class="mx-1">
                                    <div class="input-group">
                                        <span class="input-group-text">F. Inicio</span>
                                        <input id="inputFechaInicio" type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="mx-1">
                                    <div class="input-group">
                                        <span class="input-group-text">F. Término</span>
                                        <input id="inputFechaFin" type="date" class="form-control">
                                    </div>
                                </div>
                                <!-- boton buscar -->
                                <div class="mx-1">
                                    <button id="btnBuscarFecha" type="button" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Generar</button>
                                    <button id="btnReportePdf" type="button" class="btn btn-danger btn-sm">
                                        <i class="bi bi-file-pdf"></i>
                                    </button>
                                    <button id="btnReporteExcel" type="button" class="btn btn-success btn-sm">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-stripe">
                                <thead class="p-0">
                                    <tr class="table-dark p-0">
                                        <th class="px-1 py-1">#</th>
                                        <th class="px-1 py-1">Producto</th>
                                        <th class="px-1 py-1 text-center">Comprobante</th>
                                        <th class="px-1 py-1 text-center">Entrada</th>
                                        <th class="px-1 py-1 text-center">Salida</th>
                                        <th class="px-1 py-1 text-center">Fecha</th>
                                        <th class="px-1 py-1 text-center">Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaKardex"></tbody>
                            </table>
                        </div>
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