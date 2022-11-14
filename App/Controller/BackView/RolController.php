<?php

namespace App\Controller\BackView;

use App\Model\Roles;
use System\Controller;

class RolController extends Controller
{
    public function __construct()
    {
        //ejecutar para proteger la rutas cuando inicia sesion
        //enviar la sesion y el parametro principal de la url
        $this->middleware('auth');
    }

    public function index()
    {
        return view('roles.index', [
            'titleGlobal' => 'Roles',
        ]);
    }

    public function dataTable()
    {
        $roles = Roles::get();
        //cuando viene un solo objeto
        if (is_object($roles)) {
            $roles = [$roles];
        }
        //json
        echo json_encode($roles);
        exit;
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'rol_name' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Roles::create($data);
            $response = ['status' => true, 'data' => 'rol creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $rol = null;
        } else {
            $rol = Roles::first($id->id);
        }

        $response = ['status' => true, 'data' => $rol];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'rol_name' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Roles::update($data->id, $data);
            $response = ['status' => true, 'data' => 'rol actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();

        $result = Roles::delete((int)$data->id);

        $response = ['status' => true, 'data' => 'rol eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
