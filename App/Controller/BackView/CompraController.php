<?php

namespace App\Controller\BackView;

use App\Model\Compras;
use System\Controller;
use App\Model\Factura\TipoComprobante;

class CompraController extends Controller
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
        return view('compras/index', [
            'titleGlobal' => 'Compras',
        ]);
    }

    public function dataTable()
    {
        $compras = Compras::getCompras();
        //cuando viene un solo objeto
        if (is_object($compras)) {
            $compras = [$compras];
        }

        foreach ($compras as $compra) {
            $compra->fecha_compra = date('d/m/Y', strtotime($compra->fecha_compra));
        }

        //json
        echo json_encode($compras);
        exit;
    }

    public function create()
    {
        return view('compras/compras', [
            'titleGlobal' => 'Compras',
        ]);
    }

    public function tipocomprobante()
    {
        $tipoDoc = TipoComprobante::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($tipoDoc)) {
            $tipoDoc = [$tipoDoc];
        }
        //json
        echo json_encode($tipoDoc);
        exit;
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'tipo_comprobante_id' => 'required',
            'serie' => 'required',
            'fecha_compra' => 'required',
            'proveedor_id' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = Compras::create($data);
            //     ["status"]=>
            //     bool(true)
            //     ["id"]=>
            //     string(1) "1"
            //   }
            $response = ['status' => true, 'data' => 'Creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $pro = Compras::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($pro->estado == 1) ? 0 : 1;
        $result = Compras::update($data->id, ['estado' => $estado]);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
