<?php

namespace App\Controller\BackView;

use App\Model\Ventas;
use System\Controller;
use App\Model\Clientes;
use App\Model\InfoEmpresa;
use App\Model\Factura\Monedas;
use App\Help\PrintPdf\PrintPdf;
use App\Model\Factura\TipoComprobante;
use App\Model\Factura\SerieCorrelativo;

class VentaController extends Controller
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
        return view('ventas/index', [
            'titleGlobal' => 'Ventas',
        ]);
    }

    public function create()
    {
        return view('ventas/ventas', [
            'titleGlobal' => 'Ventas',
        ]);
    }

    public function tipocomprobante()
    {
        $tipoDoc = TipoComprobante::getTipoComprobante('venta');
        //cuando viene un solo objeto
        if (is_object($tipoDoc)) {
            $tipoDoc = [$tipoDoc];
        }
        //json
        echo json_encode($tipoDoc);
        exit;
    }

    public function serieCorrelativo()
    {
        $data = $this->request()->getInput();

        $serie = SerieCorrelativo::getSerieCorrelativo($data->tipo);
        //cuando viene un solo objeto
        if (is_object($serie)) {
            $serie = [$serie];
        }
        //json
        echo json_encode($serie);
        exit;
    }

    public function monedas()
    {
        $monedas = Monedas::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($monedas)) {
            $monedas = [$monedas];
        }
        //json
        echo json_encode($monedas);
        exit;
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'usuario_id' => 'required',
            'serie' => 'required',
            'cliente_id' => 'required',
            'productos' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = Ventas::create($data);
            //     ["status"]=>
            //     bool(true)
            //     ["id"]=>
            //     string(1) "1"
            //   }
            //buscar  result->id
            $venta = Ventas::find($result->id);
            //traer SerieCorrelativo
            $serie = SerieCorrelativo::find($venta->serie_id);
            //actualizar correlativo
            $dataSerie = ['correlativo' => $serie->correlativo + 1];
            $ff = SerieCorrelativo::update($serie->id, $dataSerie);


            $response = ['status' => true, 'id' => $result->id];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        // if (empty((array)$id)) {
        //     $rol = null;
        // } else {
        //     $rol = Model::first($id->id);
        // }
        // return view('folder.file', [
        //     'data' => $rol,
        // ]);
    }

    public function update()
    {
        $data = $this->request()->getInput();
        // $valid = $this->validate($data, [
        //     'name' => 'required',
        // ]);

        // if ($valid !== true) {
        //     return back()->route('route.name', [
        //         'err' =>  $valid,
        //         'data' => $data,
        //     ]);
        // } else {
        //     Model::update($data->id, $data);
        //     return redirect()->route('route.name');
        // }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        //$result = Model::delete((int)$data->id);
        //return redirect()->route('route.name');
    }

    public function reporte()
    {
        $data = $this->request()->getInput();

        $emisor = InfoEmpresa::first();
        $print = new PrintPdf;

        if (isset($data->pdfA5)) {
            $venta = Ventas::getVenta($data->pdfA5);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloA5($emisor, $venta, $cliente);
            return;
        }

        if (isset($data->pdfA4)) {
            $venta = Ventas::getVenta($data->pdfA4);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloA4($emisor, $venta, $cliente);
            return;
        }

        if (isset($data->ticket)) {
            $venta = Ventas::getVenta($data->ticket);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloTicket($emisor, $venta, $cliente);
            return;
        }

        echo 'error al generar el reporte';
        exit;
    }
}
