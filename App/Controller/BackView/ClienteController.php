<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Clientes;
use App\Model\Factura\TipoDocumento;

class ClienteController extends Controller
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
        return view('clientes/index', [
            'titleGlobal' => 'Clientes',
        ]);
    }

    public function dataTable()
    {
        $clientes = Clientes::get();
        //cuando viene un solo objeto
        if (is_object($clientes)) {
            $clientes = [$clientes];
        }
        //json
        echo json_encode($clientes);
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
            'documento' => 'required|unique:Clientes,documento',
            // 'email' => 'required',
            // 'telefono' => 'required',
            'pais' => 'required',
            'tipodoc_id' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = Clientes::create($data);
            $response = ['status' => true, 'data' => $result->id];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $cliente = null;
        } else {
            $cliente = Clientes::first($id->id);
        }

        $response = ['status' => true, 'data' => $cliente];
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
            // 'email' => 'required',
            // 'telefono' => 'required',
            'pais' => 'required',
            'tipodoc_id' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Clientes::update($data->id, $data);
            $response = ['status' => true, 'data' => 'actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = Clientes::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $cli = Clientes::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($cli->estado == 1) ? 0 : 1;
        $result = Clientes::update($data->id, ['estado' => $estado]);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function tipodocumento()
    {
        $tipoDoc = TipoDocumento::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($tipoDoc)) {
            $tipoDoc = [$tipoDoc];
        }
        //json
        echo json_encode($tipoDoc);
        exit;
    }

    public function buscar()
    {
        //busqueda para autocompletar
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Clientes::getBuscar($data->search);
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
