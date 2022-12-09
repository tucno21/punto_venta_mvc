<?php

namespace App\Controller\BackView;

use App\Model\Cajas;
use System\Controller;

class CajaController extends Controller
{
    public function __construct()
    {
        //enviar 'auth' si ha creado session sin clave de lo contrario enviar la clave
        $this->middleware('auth');
        //enviar el nombre de la ruta
        //$this->except(['users', 'users.create'])->middleware('loco');
    }

    public function index()
    {
        return view('cajas/index', [
            'titleGlobal' => 'Cajas Registradoras',
        ]);
    }

    public function dataTable()
    {
        $cajas = Cajas::get();
        //cuando viene un solo objeto
        if (is_object($cajas)) {
            $cajas = [$cajas];
        }
        //json
        echo json_encode($cajas);
        exit;
    }

    public function create()
    {
        //return view('folder/file', [
        //   'var' => 'es una variable',
        //]);
    }

    public function store()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'nombre' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Cajas::create($data);
            $response = ['status' => true, 'data' => 'creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $caja = null;
        } else {
            $caja =  Cajas::first($id->id);
        }

        $response = ['status' => true, 'data' => $caja];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'nombre' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Cajas::update($data->id, $data);
            $response = ['status' => true, 'data' => 'creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = Cajas::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $categoria = Cajas::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($categoria->estado == 1) ? 0 : 1;
        $result = Cajas::update($data->id, ['estado' => $estado]);
        // dd($result);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
