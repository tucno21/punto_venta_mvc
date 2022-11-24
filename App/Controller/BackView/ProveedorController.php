<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Proveedores;

class ProveedorController extends Controller
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
        return view('proveedores/index', [
            'titleGlobal' => 'Proveedores',
        ]);
    }

    public function dataTable()
    {
        $proveedores = Proveedores::get();
        //cuando viene un solo objeto
        if (is_object($proveedores)) {
            $proveedores = [$proveedores];
        }
        //json
        echo json_encode($proveedores);
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
        $data = $_POST;

        $valid = $this->validate($data, [
            'nombre' => 'required',
            'direccion' => 'required',
            'documento' => 'required|unique:Proveedores,documento',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = Proveedores::create($data);
            $response = ['status' => true, 'data' => $result->id];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $proveedor = null;
        } else {
            $proveedor = Proveedores::first($id->id);
        }

        $response = ['status' => true, 'data' => $proveedor];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'nombre' => 'required',
            'direccion' => 'required',
            'documento' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Proveedores::update($data->id, $data);
            $response = ['status' => true, 'data' => 'actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = Proveedores::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $pro = Proveedores::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($pro->estado == 1) ? 0 : 1;
        $result = Proveedores::update($data->id, ['estado' => $estado]);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function buscar()
    {
        //busqueda para autocompletar
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Proveedores::getBuscar($data->search);
        if (is_object($response)) {
            $response = [$response];
        }
        foreach ($response as $key => $value) {
            //obligatorio agregar ->textItem
            $response[$key]->textItem = $value->documento . ' - ' . $value->nombre;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
