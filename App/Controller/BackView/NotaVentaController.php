<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Clientes;
use App\Model\NotaVentas;
use App\Model\InfoEmpresa;
use App\Model\Factura\Monedas;
use App\Help\PrintPdf\PrintPdf;
use App\Model\Factura\TipoComprobante;
use App\Model\Factura\SerieCorrelativo;

class NotaVentaController extends Controller
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
        return view('notaventas/index', [
            'titleGlobal' => 'Ventas',
        ]);
    }

    public function dataTable()
    {
        $ventas = NotaVentas::getVentas();
        //cuando viene un solo objeto
        if (is_object($ventas)) {
            $ventas = [$ventas];
        }
        foreach ($ventas as $venta) {
            $venta->fecha_emision = date('d-m-Y', strtotime($venta->fecha_emision));
        }
        // dd($ventas);

        //json
        echo json_encode($ventas);
        exit;
    }

    public function create()
    {
        return view('notaventas/ventas', [
            'titleGlobal' => 'Ventas',
        ]);
    }


    public function tipocomprobante()
    {
        $tipoDoc = TipoComprobante::getTipoComprobante('notaventa');
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
            $result = NotaVentas::create($data);
            //     ["status"]=>
            //     bool(true)
            //     ["id"]=>
            //     string(1) "1"
            //   }
            //buscar  result->id
            $venta = NotaVentas::find($result->id);
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
        $result = NotaVentas::delete((int)$data->id);

        if ($result->status) {
            $response = ['status' => true, 'message' => 'Se elimino correctamente'];
        } else {
            $response = ['status' => false, 'message' => 'No se pudo eliminar'];
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function reporte()
    {
        $data = $this->request()->getInput();

        $emisor = InfoEmpresa::first();
        $print = new PrintPdf;

        if (isset($data->pdfA5)) {
            $venta = NotaVentas::getVenta($data->pdfA5);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloA5($emisor, $venta, $cliente);
            return;
        }

        if (isset($data->pdfA4)) {
            $venta = NotaVentas::getVenta($data->pdfA4);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloA4($emisor, $venta, $cliente);
            return;
        }

        if (isset($data->ticket)) {
            $venta = NotaVentas::getVenta($data->ticket);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloTicket($emisor, $venta, $cliente);
            return;
        }

        echo 'error al generar el reporte';
        exit;
    }

    // public function boleta()
    // {
    //     $data = $this->request()->getInput();
    //     //traer notas de venta
    //     $venta = NotaVentas::find($data->id);
    //     //
    //     $tipoComprobante = TipoComprobante::where('codigo', '03')->get();

    //     $seriemm = SerieCorrelativo::getSerieCorrelativo($tipoComprobante->codigo);
    //     if (is_object($seriemm)) {
    //         $seriemm = [$seriemm];
    //     }

    //     //cambios
    //     $venta->tipodoc = $tipoComprobante->codigo;
    //     $venta->nombre_tipodoc = $tipoComprobante->descripcion;
    //     $venta->serie_id = $seriemm[0]->id;
    //     $venta->serie = $seriemm[0]->serie;
    //     $venta->correlativo = $seriemm[0]->correlativo;
    //     $venta->fecha_emision = date('Y-m-d H:i:s');
    //     $venta->cuotas = "";
    //     //eliminar $venta->estado_sunat
    //     unset($venta->estado_sunat);
    //     unset($venta->estado);
    //     unset($venta->created_at);
    //     unset($venta->updated_at);
    //     unset($venta->id);
    //     // dd($venta);
    //     //enviar parametros mediante post
    //     $url = base_url() . "/ventas/create";
    //     // dd($url);
    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, (array)$venta);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     $result = curl_exec($ch);
    //     dd($result);
    //     // curl_close($ch);
    //     // $result = json_decode($result);
    //     // //actualizar correlativo
    // }

    public function updateElectronico()
    {
        $data = $this->request()->getInput();
        // $venta = NotaVentas::find($data->id);
        $mmm = ['estado_sunat' => '1', 'venta_id' => $data->venta_id];
        $estado = NotaVentas::update($data->id, $mmm);
        $response = ['status' => true, 'message' => 'se actualizo correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
