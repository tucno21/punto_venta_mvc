<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/productos.js',
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
                        <h5 class="m-b-10">Panel de Productos</h5>
                        <input id="urlDataTable" type="hidden" data-url="<?= route('productos.dataTable') ?>">
                        <input id="urlCreate" type="hidden" data-url="<?= route('productos.create') ?>">
                        <input id="urlEdit" type="hidden" data-url="<?= route('productos.edit') ?>">
                        <input id="urlStatus" type="hidden" data-url="<?= route('productos.status') ?>">
                        <input id="urlDestroy" type="hidden" data-url="<?= route('productos.destroy') ?>">
                        <input id="urlUnidades" type="hidden" data-url="<?= route('productos.unidades') ?>">
                        <input id="urlCategorias" type="hidden" data-url="<?= route('productos.categorias') ?>">
                        <input id="urlAfectation" type="hidden" data-url="<?= route('productos.afectacion') ?>">
                        <input id="urlGeneral" type="hidden" data-url="<?= base_url() ?>">
                        <input id="urlVerData" type="hidden" data-url="<?= route('productos.verData') ?>">
                        <input id="urlReportePdf" type="hidden" data-url="<?= route('productos.pdf') ?>">
                        <input id="urlReporteExcel" type="hidden" data-url="<?= route('productos.excel') ?>">
                    </div>
                    <div class="">
                        <button id="btnCargarProductos" type="button" class="btn btn-success btn-sm">Cargar Producto</button>
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Registrar Producto</button>
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
                            Lista de Productos
                        </div>
                        <div class="col-md-2 text-center mt-1">
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

<div class="modal fade" id="modalInputs" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title h4" id="modalLabel">Formulario Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-1">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label mb-1">Código</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-upc"></i></span>
                            <input name="codigo" type="text" class="form-control" id="inputCodigo">
                        </div>
                    </div>
                    <div class="col-md-6  mb-3">
                        <label class="form-label mb-1">Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-list-nested"></i></span>
                            <input name="descripcion" type="text" class="form-control" id="inputDescripcion">
                        </div>
                    </div>
                    <div class="col-md-6 col-6 mb-3">
                        <label class="form-label mb-1">Stock</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-9-square"></i></span>
                            <input name="stock" type="number" class="form-control" id="inputStock" min="0">
                        </div>
                    </div>
                    <div class="col-md-6 col-6  mb-3">
                        <label class="form-label mb-1">Stock Mínimo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-sort-numeric-down-alt"></i></span>
                            <input name="stock_minimo" type="number" class="form-control" id="inputStockMin" min="0">
                        </div>
                    </div>

                    <div class="col-md-3 col-6  mb-3">
                        <label class="form-label mb-1">Precio de Compra</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-coin"></i></span>
                            <input name="precio_compra" type="number" class="form-control" id="inputPC" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-3 col-6  mb-3">
                        <label class="form-label mb-1">Precio de Venta</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-coin"></i></span>
                            <input name="precio_venta" type="number" class="form-control" id="inputPV" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="col-md-3 col-6  mb-3">
                        <label class="form-label mb-1 text-success">Igv18%</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-percent"></i></span>
                            <input type="number" class="form-control" id="inputIGV" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-6  mb-3">
                        <label class="form-label mb-1 text-success">Ganancia</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-piggy-bank"></i></span>
                            <input type="number" class="form-control" id="inputGanancia" readonly disabled>
                        </div>
                    </div>

                    <div class="col-md-6  mb-3">
                        <label class="form-label mb-1">Unidad</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-unity"></i></span>
                            <select id="inputUnidad" name="unidad_id" class="form-select">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6  mb-3">
                        <label class="form-label mb-1">Categoria</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-bookmarks"></i></span>
                            <select id="inputCategoria" name="categoria_id" class="form-select">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6  mb-3">
                        <label class="form-label mb-1">Tipo de Afectación</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-wallet2"></i></span>
                            <select id="inputTipAfec" name="tipo_afectacion_id" class="form-select">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6  mb-3">
                        <label class="form-label mb-1">Imagen</label>
                        <div class="input-group">
                            <span class="input-group-text">"<i class=" bi bi-card-image"></i></span>
                            <input name="imagen" type="file" class="form-control inputFoto" id="inputImagen">
                            <select id="inputImagen" name="imagen" class="form-select">
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12  mb-3 text-center d-flex justify-content-center">
                        <div class="" style="width: 6rem;">
                            <img class="img-thumbnail card-img-top previsualizar" src="<?= base_url('/assets/img/producto.png') ?>" alt="Card image cap">
                        </div>
                        <p class="card-text">Peso máximo de 1mb</p>
                    </div>

                    <div class="col-md-12 text-center mb-2">
                        <input name="id" type="hidden" id="listId">
                        <button class="btn btn-primary" id="btnFormulario">Cambio</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalInformacion" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title h4" id="modalLabel">Información Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-1">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Código</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoCodigo" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Detalle Producto</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoDetalle" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Precio de Compra</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoPC" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Precio de Venta</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoPV" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Stock</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoStock" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Categoria</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoCategoria" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Unidad</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoUnidad" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Tipo de Afectación</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoTA" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Fecha de Creación</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoFC" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Fecha de Modificación</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infoFM" class="form-control" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Registrado por:</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input id="infouser" class="form-control" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal Excel -->
<div class="modal fade" id="modalExel" tabindex="-1" aria-labelledby="modalExel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar productos en forma masiva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="urlCargarProcutos" type="hidden" data-url="<?= route('productos.uploaddata') ?>">
                <p>Seleccione el archivo excel que descargo de ejemplo y agrego todos sus productos</p>

                <div class="mb-3">
                    <div class="input-group input-group-sm mb-3">
                        <label class="input-group-text" for="inputGroupFile01"><i class="bi bi-filetype-xls"></i></label>
                        <input type="file" id="inputDataExcel" accept=".xls,.xlsx" class="form-control" required>
                    </div>
                </div>

                <div class="text-center card-footer p-0 pb-3">
                    <a href="<?= route('productos.tablemodel') ?>" class="btn btn-success">
                        <i class="mx-1 bi bi-file-earmark-excel"></i>
                        Descargar Excel de ejemplo
                        <i class="mx-1 bi bi-arrow-down-square"></i>
                    </a>
                    <a href="<?= route('productos.codigoafectacion') ?>" target="_blank" class="btn btn-danger">
                        códigos: tipo afectacion (IGV)
                        <i class="mx-1 bi bi-file-earmark-pdf"></i>
                    </a>
                </div>


                <div class="text-center card-footer p-0 pb-3">
                    <button class="btn btn-dark" id="btnCargarExcel">Subir EXCEL</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>