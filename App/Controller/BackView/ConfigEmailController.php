<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\ConfigEmail;

class ConfigEmailController extends Controller
{
    public function __construct()
    {
        //enviar 'auth' si ha creado session sin clave de lo contrario enviar la clave
        //$this->middleware('auth');
        //enviar el nombre de la ruta
        //$this->except(['users', 'users.create'])->middleware('loco');
    }

    public function index()
    {
        return view('configEmails/index', [
            'titleGlobal' => 'Configuración de Correo',
        ]);
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'servidor' => 'required',
            'correo_servidor' => 'required',
            'contrasena_servidor' => 'required',
            'puerto' => 'required',
            'tipo_protocolo' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = ConfigEmail::update(1, $data);
            $response = ['status' => true, 'message' => 'Configuración de envio de correos actualizada correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
