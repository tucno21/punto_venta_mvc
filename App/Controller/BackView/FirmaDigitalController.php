<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\FirmaDigital;

class FirmaDigitalController extends Controller
{
    public function __construct()
    {
        //enviar 'auth' si ha creado session sin clave de lo contrario enviar la clave
        $this->middleware('auth');
        //enviar el nombre de la ruta
        //$this->except(['users', 'users.create'])->middleware('loco');
    }

    public function update()
    {
        $data = $_POST;
        if (!empty($_FILES)) {
            $data = array_merge($data, $_FILES);
        }
        $data = (object)$data;

        $valid = $this->validate($data, [
            'password_firma' => 'required',
            'fecha_venc' => 'required',
            'firma_digital' => 'requiredFile',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $nameDigital = "certificado.pfx";
            $ruta = DIR_APP . '/Library/ApiFacturador/certificado/' . $nameDigital;

            if (move_uploaded_file($data->firma_digital['tmp_name'],  $ruta)) {

                $data->fecha = date('Y-m-d');

                FirmaDigital::update(1, $data);

                $response = ['status' => true, 'data' => 'Los datos de la firma se actualizaron correctamente'];
                echo json_encode($response);
                exit;
            }
        }
    }

    public function ver()
    {
        $firma = FirmaDigital::get();
        echo json_encode($firma);
        exit;
    }
}
