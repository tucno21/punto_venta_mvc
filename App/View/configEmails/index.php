<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
    base_url . '/assets/js/configEmails.js',
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
                        <h5 class="m-b-10">Configuración de Servidor de envios de Correo</h5>
                        <input id="urlEdit" type="hidden" data-url="<?= route('configEmails.index') ?>">
                        <input id="urlGeneral" type="hidden" data-url="<?= base_url() ?>">
                    </div>
                    <div class="">
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
                    <h5>Servidor Correo</h5>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6  mt-3">
                            <label class="form-label mb-1">Servidor del Correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hdd-network"></i></span>
                                <input type="text" class="form-control" id="inputServidor" placeholder="smtp.gmail.com">
                            </div>
                        </div>

                        <div class="col-md-6  mt-3">
                            <label class="form-label mb-1">Correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                                <input type="text" class="form-control" id="inputCorreo" placeholder="correo@correo.com">
                            </div>
                        </div>

                        <div class="col-md-4  mt-3">
                            <label class="form-label mb-1">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="text" class="form-control" id="inputPassword" placeholder="czfluoplsuelq333">
                            </div>
                        </div>

                        <div class="col-md-4  mt-3">
                            <label class="form-label mb-1">Puerto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-usb-mini"></i></span>
                                <input type="text" class="form-control" id="inputPuerto" placeholder="465">
                            </div>
                        </div>

                        <div class="col-md-4  mt-3">
                            <label class="form-label mb-1">Tipo de Protocolo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <select id="inputProtocolo" class="form-select">
                                    <option value="ssl">SSL</option>
                                    <option value="tls">TLS</option>
                                </select>
                            </div>
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

<?php include ext('layoutdash.footer') ?>