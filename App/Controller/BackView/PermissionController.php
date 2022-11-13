<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Permissions;

class PermissionController extends Controller
{
    public function __construct()
    {
        //ejecutar para proteger la rutas cuando inicia sesion
        //enviar la sesion y el parametro principal de la url
        $this->middleware('auth');
    }

    public function index()
    {
        return view('permission.index', [
            'titulo' => 'panel de permisos',
        ]);
    }

    public function listaPermissions()
    {
        $permissions = Permissions::get();

        //cuando viene un solo objeto
        if (is_object($permissions)) {
            $permissions = [$permissions];
        }

        //json
        echo json_encode($permissions);
        exit;
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'per_name' => 'required',
            'description' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Permissions::create($data);
            $response = ['status' => true, 'data' => 'permiso creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $per = null;
        } else {
            $per = Permissions::first($id->id);
        }

        $response = ['status' => true, 'data' => $per];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'per_name' => 'required',
            'description' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Permissions::update($data['id'], $data);
            $response = ['status' => true, 'data' => 'permiso actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        Permissions::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'permiso eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
