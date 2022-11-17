<?php

namespace App\Controller\Factura;

use System\Controller;
use App\Model\Factura\TipoDocumento;

class TipoDocumentoController extends Controller
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
        return view('tipodocumento/index', [
            'titleGlobal' => 'Tipo Documento',
        ]);
    }

    public function dataTable()
    {
        $tipoDoc = TipoDocumento::get();
        //cuando viene un solo objeto
        if (is_object($tipoDoc)) {
            $tipoDoc = [$tipoDoc];
        }
        //json
        echo json_encode($tipoDoc);
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
            'codigo' => 'required',
            'descripcion' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            TipoDocumento::create($data);
            $response = ['status' => true, 'data' => 'creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $tipDoc = null;
        } else {
            $tipDoc =  TipoDocumento::first($id->id);
        }

        $response = ['status' => true, 'data' => $tipDoc];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'codigo' => 'required',
            'descripcion' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            TipoDocumento::update($data->id, $data);
            $response = ['status' => true, 'data' => 'creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = TipoDocumento::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $tipoDoc = TipoDocumento::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($tipoDoc->estado == 1) ? 0 : 1;
        $result = TipoDocumento::update($data->id, ['estado' => $estado]);
        // dd($result);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
