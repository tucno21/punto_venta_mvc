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
    </style>

</head>

<div class="auth-wrapper">
    <div class="auth-content">

        <div class="card">
            <div class="card-body text-center">
                <h5 class="mb-4">Recuperar Contraseña</h5>
                <img src="<?= base_url('/assets/img/logo_inicio.jpg') ?>" class="img-radius mb-4" alt="Punto de venta" style="width: 4rem;">

                <form action="<?= route('login.reset') ?>" method="POST" id="formResetPassword">

                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="inputPassword" placeholder="Contraseña">
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="inputRepeatPassword" placeholder="Repetir Contraseña">
                    </div>

                    <button class="btn btn-lg btn-primary mb-4" type="submit">Actualizar</button>
                </form>

                <div id="verMensage">
                    <!-- <div class="alert alert-danger p-1">
                        A simple danger alert—check it out!
                    </div> -->
                </div>

                <p class="mb-0 text-muted"><a href="<?= route("login.index") ?>">Iniciar Sessión</a></p>
            </div>
        </div>
    </div>
    <!-- [ profile-settings ] end -->
</div>

<!-- agregar el script -->
<script src="<?= base_url('/assets/js/resetpassword.js') ?>"></script>
</body>

</html>