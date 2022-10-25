<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensaje de Error</title>
</head>

<body>

    <div>
        <div style=' background-color:#A93226; padding:0.3rem;text-align:center;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#ffffff;margin:0;'>Error Detectado</p>
        </div>
        <div style=' background-color:#F1C40F; padding:0.3rem;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#000000;margin:0;width:15%; float:left; display:block'>Mensaje</p>
            <p style='font-weight:normal;color:#273746;margin:0;display: inline'>: <?= $mensaje ?></p>
        </div>
        <div style=' background-color:#F1C40F; padding:0.3rem;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#000000;margin:0;width:15%; float:left; display:block'>Archivo</p>
            <p style='font-weight:normal;color:#273746;margin:0;display: inline'>: <?= $archivo ?></p>
        </div>
        <div style=' background-color:#F1C40F; padding:0.3rem;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#000000;margin:0;width:15%; float:left; display:block'>En la linea</p>
            <p style='font-weight:normal;color:#273746;margin:0;display: inline'>: <?= $line ?></p>
        </div>
    </div>

</body>

</html>