<!DOCTYPE html>
<html lang="es">

<head>
    <title>Punto de Venta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />

    <!-- Favicon icon -->
    <link rel="icon" href="https://dashboardkit.io/bootstrap/assets/images/favicon.svg" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="<?= base_url('/assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/css/customizer.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/css/icon/bootstrap-icons.css') ?>">

    <style>
        .auth-wrapper {
            background-color: #2A7B9B;
        }

        .btn-primary {
            background-color: #2A7B9B;
            border-color: #2A7B9B;
        }

        .btn-primary:hover {
            background-color: #3D3D6B;
            border-color: #3D3D6B;
        }

        /* .form-control {
            border-color: #2A7B9B;
        } */

        .form-control:focus {
            border-color: #2A7B9B;
            box-shadow: 0 0 0 0.2rem rgba(42, 123, 155, 0.25);
        }

        .auth-wrapper .card {
            padding: 0;
        }

        .auth-wrapper .auth-content:not(.container) {
            width: 700px;
        }

        .border-my {
            border: 1px solid #2A7B9B !important;
        }

        .was-validated .form-control:invalid,
        .form-control.is-invalid {
            border-color: #ea4d4d !important;
        }
    </style>

</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-content">

            <div class="card">
                <div class="card-header text-center bg-dark ">
                    <h1 class="fs-3 text-white">Buscar Comprobante Electrónico</h1>
                    <input type="hidden" id="urlTipoComprobante" data-url="<?= route('searchDocuments.tipoComprobante') ?>">
                    <input id="urlDownloadXml" type="hidden" data-url="<?= route('searchDocuments.downloadxml') ?>">
                    <input id="urlDownloadCdr" type="hidden" data-url="<?= route('searchDocuments.downloadcdr') ?>">
                    <input id="urlReportePdf" type="hidden" data-url="<?= route('searchDocuments.reporte') ?>">
                </div>
                <div class="card-body p-4">
                    <form action="<?= route('searchDocuments.index') ?>" method="POST" id="formSearchDocument">
                        <div class="row">

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Tipo Documento<span class="text-danger"> *</span></label>
                                    <select class="form-select border-my" id="inputSelectTipoDoc"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Fecha de emisión<span class="text-danger"> *</span></label>
                                    <input type="date" autocomplete="off" class="form-control border-my" id="inputFechaEmision">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Serie<span class="text-danger"> *</span></label>
                                    <input type="text" autocomplete="off" class="form-control border-my" id="inputSerie" placeholder="F001">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Número / correlativo<span class="text-danger"> *</span></label>
                                    <input type="number" autocomplete="off" class="form-control border-my" id="inputCorrelativo" placeholder="5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Número Cliente (RUC/DNI)<span class="text-danger"> *</span></label>
                                    <input type="text" autocomplete="off" class="form-control border-my" id="inputNumberDocumento">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Monto total<span class="text-danger"> *</span></label>
                                    <input type="number" autocomplete="off" class="form-control border-my" id="inputTotal" step="0.01" min="0">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-lg btn-dark float-end" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>

                    <div id="verMensage" class="mt-2">
                        <!-- <div class="alert alert-danger p-1">
                        A simple danger alert—check it out!
                    </div> -->
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- agregar el script -->
    <script src="<?= base_url('/assets/js/searchdocument.js') ?>"></script>
</body>

</html>