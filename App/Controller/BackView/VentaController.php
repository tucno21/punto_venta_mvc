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
use App\Library\ApiFacturador\EnviarSunat;
use App\Library\ApiFacturador\GeneradorXml;

class VentaController extends Controller
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
        return view('ventas/index', [
            'titleGlobal' => 'Ventas',
        ]);
    }

    public function dataTable()
    {
        $ventas = Ventas::getVentas();
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

            //crear xml y firmar

            $estado = $this->generarXML($venta->id);

            if ($estado->success) {
                $response = ['status' => true, 'id' => $result->id, 'Message' => $estado->Message];
            } else {
                $response = ['status' => false, 'Message' => $estado->Message];
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function generarXML($id)
    {
        // $id = 35;
        $emisor = InfoEmpresa::first();
        $venta = Ventas::getVenta($id);
        $cliente = Clientes::getCliente($venta->cliente_id);

        $tipo_precio = [
            '10'    =>  '01',
            '11'    =>  '02',
            '12'    =>  '02',
            '13'    =>  '02',
            '14'    =>  '02',
            '15'    =>  '02',
            '16'    =>  '02',
            '20'    =>  '01',
            '21'    =>  '02',
            '30'    =>  '01',
            '31'    =>  '02',
            '32'    =>  '02',
            '33'    =>  '02',
            '34'    =>  '02',
            '35'    =>  '02',
            '36'    =>  '02',
        ];

        $detalle = json_decode($venta->productos);
        $item = 1;
        foreach ($detalle as $k => $v) {
            $detalle[$k]->item = $item;
            if ($v->codigo_afectacion_alt == '10') {
                $detalle[$k]->tipo_precio = $tipo_precio[$v->codigo_afectacion_alt];
                $detalle[$k]->valor_unitario = round($v->precio_unitario / 1.18, 2);
                $detalle[$k]->valor_unitario_final = round($v->precio_unitario / 1.18, 2);
                $detalle[$k]->igv_1 = round(($v->precio_unitario / 1.18) * $v->cantidad * 0.18, 2);
                $detalle[$k]->igv_2 = round(($v->precio_unitario / 1.18) * $v->cantidad * 0.18, 2);
                $detalle[$k]->valor_total = round($v->precio_unitario * $v->cantidad / 1.18, 2);
                $detalle[$k]->importe_total = round($v->precio_unitario * $v->cantidad, 2);
            } else if ($v->codigo_afectacion_alt == '11' || $v->codigo_afectacion_alt == '12' || $v->codigo_afectacion_alt == '13' || $v->codigo_afectacion_alt == '14' || $v->codigo_afectacion_alt == '15' || $v->codigo_afectacion_alt == '16') {
                $detalle[$k]->tipo_precio = $tipo_precio[$v->codigo_afectacion_alt];
                $detalle[$k]->valor_unitario = round($v->precio_unitario, 2);
                $detalle[$k]->valor_unitario_final = 0;
                $detalle[$k]->igv_1 = 0;
                $detalle[$k]->igv_2 = round($v->precio_unitario  * $v->cantidad * 0.18, 2);
                $detalle[$k]->valor_total = round($v->precio_unitario * $v->cantidad, 2);
                $detalle[$k]->importe_total = round($v->precio_unitario * $v->cantidad, 2);
            } else if ($v->codigo_afectacion_alt == '20' || $v->codigo_afectacion_alt == '30') {
                $detalle[$k]->tipo_precio = $tipo_precio[$v->codigo_afectacion_alt];
                $detalle[$k]->valor_unitario = round($v->precio_unitario, 2);
                $detalle[$k]->valor_unitario_final = round($v->precio_unitario, 2);
                $detalle[$k]->igv_1 = 0;
                $detalle[$k]->igv_2 = 0;
                $detalle[$k]->valor_total = round($v->precio_unitario * $v->cantidad, 2);
                $detalle[$k]->importe_total = round($v->precio_unitario * $v->cantidad, 2);
            } else if ($v->codigo_afectacion_alt == '21' || $v->codigo_afectacion_alt == '31' || $v->codigo_afectacion_alt == '32' || $v->codigo_afectacion_alt == '33' || $v->codigo_afectacion_alt == '34' || $v->codigo_afectacion_alt == '35' || $v->codigo_afectacion_alt == '36') {
                $detalle[$k]->tipo_precio = $tipo_precio[$v->codigo_afectacion_alt];
                $detalle[$k]->valor_unitario = round($v->precio_unitario, 2);
                $detalle[$k]->valor_unitario_final = 0;
                $detalle[$k]->igv_1 = 0;
                $detalle[$k]->igv_2 = 0;
                $detalle[$k]->valor_total = round($v->precio_unitario * $v->cantidad, 2);
                $detalle[$k]->importe_total = round($v->precio_unitario * $v->cantidad, 2);
            }
            $item++;
        }
        $venta->productos = json_encode($detalle);
        $venta->fecha_emision = date('Y-m-d', strtotime($venta->fecha_emision));

        //eviar al generador de xml y firmar
        $xml = new GeneradorXml();
        $result = $xml->xml($emisor, $venta, $cliente);

        if ($result->success) {
            $nombreXML = $emisor->ruc . '-' . $venta->tipodoc . '-' . $venta->serie . '-' . $venta->correlativo;

            $data = ['nombre_xml' => $nombreXML];
            $result = Ventas::update($venta->id, $data);

            if ($result->status) {
                $return = ['success' => true, 'Message' => 'Se genero el XML correctamente', 'nombre_xml' => $nombreXML];
                return (object)$return;
            }
        }

        return $result;
    }

    public function enviarSunat()
    {
        $data = $this->request()->getInput();
        $emisor = InfoEmpresa::first();
        //buscar nombre_xml  en venta
        $venta = Ventas::select('nombre_xml')->where('id', $data->id)->get();

        $enviar = new EnviarSunat();

        $result = $enviar->enviarComprobante($emisor, $venta->nombre_xml);

        if ($result['success']) {
            $mmm = ['estado_sunat' => '1'];
            $estado = Ventas::update($data->id, $mmm);
        }

        echo json_encode($result);
        exit;
    }

    public function downloadxml()
    {
        $data = $this->request()->getInput();
        $rutaxml = DIR_APP . '/Library/ApiFacturador/files_factura/xml_files/' . $data->xml . '.XML';

        if (file_exists($rutaxml)) {

            $carpetaXML = DIR_PUBLIC . '/xml_files/';
            if (!file_exists($carpetaXML)) {
                mkdir($carpetaXML, 0777, true);
            }
            $rutaPublic = DIR_PUBLIC . '/xml_files/' . $data->xml . '.XML';

            copy($rutaxml, $rutaPublic);

            $archivo = base_url('/xml_files/' . $data->xml . '.XML');
            header('Content-disposition: attachment; filename=' . $data->xml . '.XML');
            header('Content-type: application/xml');
            readfile($archivo);

            unlink($rutaPublic);
            rmdir($carpetaXML);

            exit;
        } else {
            echo 'No existe el archivo';
        }
    }
    public function downloadcdr()
    {
        $data = $this->request()->getInput();
        $rutaxmlZIP = DIR_APP . '/Library/ApiFacturador/files_factura/cdr_files/R-' . $data->xml . '.ZIP';

        if (file_exists($rutaxmlZIP)) {

            // descargar $rutaxmlZIP
            $carpetaZIP = DIR_PUBLIC . '/cdr_files/';
            if (!file_exists($carpetaZIP)) {
                mkdir($carpetaZIP, 0777, true);
            }
            $rutaPublic = DIR_PUBLIC . '/cdr_files/R-' . $data->xml . '.ZIP';

            copy($rutaxmlZIP, $rutaPublic);

            $archivo = base_url('/cdr_files/R-' . $data->xml . '.ZIP');
            header('Content-disposition: attachment; filename=R-' . $data->xml . '.ZIP');
            header('Content-type: application/zip');
            readfile($archivo);

            unlink($rutaPublic);
            exit;
        } else {
            echo 'No existe el archivo';
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
