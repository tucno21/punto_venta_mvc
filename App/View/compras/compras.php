<?php
$linksCss2 = [
    base_url . '/assets/css/listaBusqueda.css',
];

$linksScript2 = [
    base_url . '/assets/js/compras.js',
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
                        <h5 class="m-b-10">Formulario Compras</h5>
                        <input id="urlCreate" type="hidden" data-url="<?= route('compras.create') ?>">
                        <input id="urlTipoComprobante" type="hidden" data-url="<?= route('compras.tipocomprobante') ?>">
                        <input id="urlCreateProveedor" type="hidden" data-url="<?= route('proveedores.create') ?>">
                        <input id="urlBuscarProveedor" type="hidden" data-url="<?= route('proveedores.buscar') ?>">
                    </div>
                    <div class="">
                        <a href="<?= route('compras.index') ?>" class="btn btn-primary btn-sm">Volver</a>
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
                    <h5>Complete todos los Campos</h5>
                </div>
                <div class="card-body p-2">
                    <!-- comprobantes -->
                    <div class="row">
                        <div class="col-md-2 mb-1">
                            <label for="inputTipoComprobante" class="form-label m-0">T. Comprobante</label>
                            <select class="form-select" id="inputTipoComprobante"></select>
                        </div>
                        <div class="col-md-3 mb-1">
                            <label for="inputSerie" class="form-label m-0">Serie Comprobante</label>
                            <input type="text" class="form-control" id="inputSerie">
                        </div>
                        <div class=" col-md-3 mb-1">
                            <label for="inputFechaCompra" class="form-label m-0">Fecha</label>
                            <input type="date" class="form-control" id="inputFechaCompra">
                        </div>
                        <div class="col-md-4 mb-1">
                            <label for="inputBuscarProveedor" class="form-label m-0">Proveedor
                                <span class="text-success" id="btnRegistrar">[+Registrar]</span>
                            </label>
                            <input type="hidden" id="inputProveedorId">
                            <input type="hidden" id="inputUserId" value="<?= session()->user()->id ?>">
                            <div class=" input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="inputBuscarProveedor" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- productos -->
                    <div class="row mt-2">
                        <div class="col">
                            <div class="btn-group btn-group-togle btn-group-sm">
                                <label class="btn btn-warning">
                                    <input id="checkedBarcode" class="form-check-input checkCompra" type="radio" name="opcion" checked="" data-link="<?= route('productos.barcodecompra') ?>"><i class="bi bi-upc"></i> Barcode
                                </label>
                                <label class="btn btn-success">
                                    <input id="checkedNombre" class="form-check-input checkCompra" type="radio" name="opcion" data-link="<?= route('productos.inputSearchcompra') ?>"><i class="bi bi-list-task"></i> Nombre
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

                        <div class="table-responsive">
                            <table class="table table-stripe">
                                <thead class="p-0">
                                    <tr class="table-dark p-0">
                                        <th class="px-1 py-1">Producto</th>
                                        <th class="px-1 py-1">Cantidad</th>
                                        <th class="px-1 py-1">P.Compra</th>
                                        <th class="px-1 py-1">P.Venta</th>
                                        <th class="px-1 py-1 text-center">SubTotal</th>
                                        <th class="px-1 py-1 text-center"><i class="bi bi-trash3"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCompras"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- total -->
                    <div class="row">
                        <div class="col-md-9"></div>
                        <div class="col-md-3">
                            <div class="">
                                <label class="form-label mb-1">Total Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/.</span>
                                    <input name="total" id="inputTotalCompra" type="text" class="form-control" disabled="">
                                </div>
                            </div>

                            <div class="text-center mt-2">
                                <button class="btn btn-dark" type="button" id="btnEnviarComprar">Completar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<div class=" modal fade" id="modalInputs" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modalLabel">Formulario Registrar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">DNI/RUC</label>
                            <div class="input-group">
                                <button class="btn btn-outline-dark" type="button" id="btnBuscarCliente">Buscar</button>
                                <input name="documento" type="number" class="form-control" id="inputDocumento">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">Nombres</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input name="nombre" type="text" class="form-control" id="inputNombre">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input name="direccion" type="text" class="form-control" id="inputDireccion">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">Celular</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input name="telefono" type="text" class="form-control" id="inputTelefono">
                            </div>
                        </div>

                        <div class="col-md-12 text-center mt-3">
                            <input name="id" type="hidden" id="listId">
                            <button class="btn btn-primary" id="btnFormularioRegistrar">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>