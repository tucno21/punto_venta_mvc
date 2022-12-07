<?php
$linksCss2 = [
    base_url . '/assets/css/listaBusqueda.css',
];

$linksScript2 = [
    base_url . '/assets/js/notaventas.js',
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
                        <h5 class="m-b-10">Formulario Ventas Internas</h5>
                        <input id="urlCreate" type="hidden" data-url="<?= route('notaventas.create') ?>">
                        <input id="urlTipoComprobante" type="hidden" data-url="<?= route('notaventas.tipocomprobante') ?>">
                        <input id="urlCorrelativo" type="hidden" data-url="<?= route('notaventas.serieCorrelativo') ?>">
                        <input id="urlMonedas" type="hidden" data-url="<?= route('notaventas.monedas') ?>">
                        <input id="urlTipoDoc" type="hidden" data-url="<?= route('clientes.tipodocumento') ?>">
                        <input id="urlCreateCliente" type="hidden" data-url="<?= route('clientes.create') ?>">
                        <input id="urlProductosId" type="hidden" data-url="<?= route('productos.edit') ?>">
                        <input id="urlBuscarCliente" type="hidden" data-url="<?= route('clientes.buscar') ?>">
                    </div>
                    <div class="">
                        <a href="<?= route('notaventas.index') ?>" class="btn btn-primary btn-sm">Volver</a>
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
                        <div class="col-md-2 mb-1">
                            <label for="inputSerieId" class="form-label m-0">Serie</label>
                            <select class="form-select" id="inputSerieId"></select>
                        </div>
                        <div class="col-md-1 mb-1">
                            <label for="inputCorrelativo" class="form-label m-0">Correlativo</label>
                            <input type="text" class="form-control" id="inputCorrelativo" readOnly>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label for="inputMoneda" class="form-label m-0">Moneda</label>
                            <select class="form-select" id="inputMoneda"></select>
                        </div>
                        <div class=" col-md-2 mb-1">
                            <label for="inputFechaVenta" class="form-label m-0">Fecha</label>
                            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" readonly disabled>
                            <input type="hidden" id="inputFechaVenta" value="<?= date('Y-m-d H:i:s') ?>">
                        </div>
                        <div class="col-md-3 mb-1">
                            <label for="inputBuscarCliente" class="form-label m-0">Cliente</label>
                            <span class="text-success" id="btnRegistrar">[+Registrar]</span>
                            <input type="hidden" id="inputClienteId">
                            <input type="hidden" id="inputUserId" value="<?= session()->user()->id ?>">
                            <div class=" input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="inputBuscarCliente" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- productos -->
                    <div class="row mt-2">
                        <div class="col">
                            <div class="btn-group btn-group-togle btn-group-sm">
                                <label class="btn btn-warning">
                                    <input id="checkedBarcode" class="form-check-input checkCompra" type="radio" name="opcion" checked="" data-link="<?= route('productos.barcode') ?>"><i class="bi bi-upc"></i> Barcode
                                </label>
                                <label class="btn btn-success">
                                    <input id="checkedNombre" class="form-check-input checkCompra" type="radio" name="opcion" data-link="<?= route('productos.inputSearch') ?>"><i class="bi bi-list-task"></i> Nombre
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
                                        <th class="px-1 py-1">Valor U.</th>
                                        <th class="px-1 py-1">Precio U.</th>
                                        <th class="px-1 py-1 text-center">SubTotal</th>
                                        <th class="px-1 py-1 text-center"><i class="bi bi-trash3"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaventas"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-5" id="condicionpago"></div>
                                <div class="col-5" id="cantidad_cuotas"></div>
                            </div>
                            <div class="row mt-2" id="cuotasContainer">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <!-- OP. Gratuitas -->
                            <div class="row rowGratuita d-none">
                                <div class="col-6 text-end">
                                    <label class="form-label mb-1">OP. Gratuitas</label>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/.</span>
                                        <input id="inputGratuita" type="text" class="form-control" disabled="">
                                    </div>
                                </div>
                            </div>
                            <!-- OP. Exoneradas -->
                            <div class="row rowExonerada d-none">
                                <div class="col-6 text-end">
                                    <label class="form-label mb-1">OP. Exoneradas</label>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/.</span>
                                        <input id="inputExonerada" type="text" class="form-control" disabled="">
                                    </div>
                                </div>
                            </div>
                            <!-- OP. Inafectas -->
                            <div class="row rowInafecta d-none">
                                <div class="col-6 text-end">
                                    <label class="form-label mb-1">OP. Inafectas</label>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/.</span>
                                        <input id="inputInafecta" type="text" class="form-control" disabled="">
                                    </div>
                                </div>
                            </div>
                            <!-- OP. Gravadas -->
                            <div class="row rowGravada d-none">
                                <div class="col-6 text-end">
                                    <label class="form-label mb-1">OP. Gravadas</label>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/.</span>
                                        <input id="inputGrabada" type="text" class="form-control" disabled="">
                                    </div>
                                </div>
                            </div>
                            <!-- IGV (18%) -->
                            <div class="row">
                                <div class="col-6 text-end">
                                    <label class="form-label mb-1">IGV (18%)</label>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/.</span>
                                        <input id="inputInpuestoTotal" type="text" class="form-control" disabled="">
                                    </div>
                                </div>
                            </div>
                            <!-- TOTAL -->
                            <div class="row">
                                <div class="col-6 text-end">
                                    <label class="form-label mb-1">Total Venta</label>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm ">
                                        <span class="input-group-text border border-dark">S/.</span>
                                        <input id="inputTotalVenta" type="text" class="form-control border border-dark" disabled="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BTN ENVIAR -->
                    <div class="row">
                        <div class="col-md-9"></div>
                        <div class="col-md-3">
                            <div class="text-center mt-2">
                                <input type="hidden" id="inputIgv_gratuita">
                                <input type="hidden" id="inputIgv_exonerada">
                                <input type="hidden" id="inputIgv_inafecta">
                                <input type="hidden" id="inputIgv_grabada">
                                <button class="btn btn-dark" type="button" id="btnEnviarVentas">Completar</button>
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
                <h5 class="modal-title h4" id="modalLabel">Formulario Registrar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">Tipo documento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-input-cursor"></i></span>
                                <select id="inputTipoDoc" name="tipodoc_id" class="form-select"></select>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">DNI/RUC</label>
                            <div class="input-group">
                                <button class="btn btn-outline-dark" type="button" id="btnBuscarCliente">Buscar</button>
                                <input name="documento" type="number" class="form-control" id="inputDocumento">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">Pais</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-globe-americas"></i></span>
                                <input name="pais" type="text" class="form-control" id="inputPais">
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
                        <div class="col-md-6 mt-3">
                            <label class="form-label mb-1">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input name="email" type="email" class="form-control" id="inputEmail">
                            </div>
                        </div>

                        <div class="col-md-12 text-center mt-3">
                            <button class="btn btn-primary" id="btnFormularioRegistrar">Registrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>