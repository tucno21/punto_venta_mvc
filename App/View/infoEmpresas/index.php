<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/infoEmpresas.js',
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
                        <h5 class="m-b-10">Datos de la Empresa</h5>
                        <input id="urlEdit" type="hidden" data-url="<?= route('infoEmpresas.edit') ?>">
                        <input id="urlGeneral" type="hidden" data-url="<?= base_url() ?>">
                    </div>
                    <div class="">
                        <button id="btnCrear" type="button" class="btn btn-primary btn-sm">Firma Digital</button>
                        <button id="" type="button" class="btn btn-danger btn-sm">Algo</button>
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
                    <h5>Empresa</h5>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-1  mt-3">
                            <label class="form-label mb-1">N. Com.</label>
                            <input name="tipodoc" value="6" type="number" class="form-control" id="inputTipoDoc" readonly>
                        </div>

                        <div class="col-md-2  mt-3">
                            <label class="form-label mb-1">RUC</label>
                            <input name="ruc" type="number" class="form-control" id="inputRuc">
                        </div>

                        <div class="col-md-4  mt-3">
                            <label class="form-label mb-1">RAZÓN SOCIAL</label>
                            <input name="razon_social" type="text" class="form-control" id="inputRazon">
                        </div>

                        <div class="col-md-5  mt-3">
                            <label class="form-label mb-1">NOMBRE COMERCIAL</label>
                            <input name="nombre_comercial" type="text" class="form-control" id="inputNombre">
                        </div>
                        <!-- GGGGGGGGGGG -->
                        <div class="col-md-4  mt-3">
                            <label class="form-label mb-1">DIRECCIÓN</label>
                            <input name="direccion" type="text" class="form-control" id="inputDireccion">
                        </div>

                        <div class="col-md-1  mt-3">
                            <label class="form-label mb-1">PAIS</label>
                            <input name="pais" value="PE" type="text" class="form-control" id="inputPais" readonly>
                        </div>
                        <div class="col-md-4  mt-3">
                            <label class="form-label mb-1">DEPARTAMENTO</label>
                            <input name="departamento" type="text" class="form-control" id="inputDepart">
                        </div>
                        <div class="col-md-3  mt-3">
                            <label class="form-label mb-1">PROVINCIA</label>
                            <input name="provincia" type="text" class="form-control" id="inputProvincia">
                        </div>
                        <!-- GGGGGGGGGGG -->
                        <div class="col-md-3  mt-3">
                            <label class="form-label mb-1">DISTRITO</label>
                            <input name="distrito" type="text" class="form-control" id="inputDistrito">
                        </div>
                        <div class="col-md-2  mt-3">
                            <label class="form-label mb-1">UBIGEO</label>
                            <input name="ubigeo" type="number" class="form-control" id="inputUbigeo">
                        </div>
                        <div class="col-md-2  mt-3">
                            <label class="form-label mb-1">CELULAR</label>
                            <input name="telefono" type="number" class="form-control" id="inputTelf">
                        </div>
                        <div class="col-md-3  mt-3">
                            <label class="form-label mb-1">CORREO</label>
                            <input name="email" type="email" class="form-control" id="inputEmail">
                        </div>
                        <div class="col-md-2  mt-3">
                            <label class="form-label mb-1">Usuario Secundario</label>
                            <input name="usuario_secundario" type="text" class="form-control" id="inputuser">
                        </div>
                        <!-- GGGGGGGGGGG -->
                        <div class="col-md-2  mt-3">
                            <label class="form-label mb-1">Clave de Usuario</label>
                            <input name="clave_usuario_secundario" type="text" class="form-control" id="inputClave">
                        </div>
                        <div class="col-md-10  mt-3">
                            <label class="form-label mb-1">Descripción o Mensaje</label>
                            <input name="descripcion" type="text" class="form-control" id="inputDescripcion">
                        </div>
                        <div class="col-md-6  mt-3">
                            <label class="form-label mb-1">Logo</label>
                            <input name="logo" type="file" class="form-control inputFoto" id="inputLogo">
                        </div>

                        <div class="col-md-6 mt-3 text-center d-flex justify-content-center">
                            <div class="" style="width: 6rem;">
                                <img class="img-thumbnail card-img-top previsualizar" src="<?= base_url('/assets/img/producto.png') ?>" alt="Card image cap">
                            </div>
                            <p class="card-text">Peso máximo de 1mb</p>
                        </div>


                        <div class="col-md-12 text-center mt-3">
                            <input name="id" type="hidden" id="listId">
                            <button class="btn btn-primary" id="btnFormulario">Actualizar</button>
                        </div>

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
                        <button class="btn btn-primary" id="">Cambio</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ext('layoutdash.footer') ?>