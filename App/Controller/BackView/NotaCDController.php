<?php

namespace App\Controller\BackView;

use System\Model;
use App\Model\Ventas;
use App\Model\NotasCD;
use System\Controller;
use App\Model\Clientes;
use App\Model\InfoEmpresa;
use App\Help\PrintPdf\PrintPdf;
use App\Model\Factura\TipoComprobante;
use App\Model\Factura\SerieCorrelativo;
use App\Model\Factura\TablaParametrica;
use App\Library\ApiFacturador\EnviarSunat;
use App\Library\ApiFacturador\GeneradorXml;

class NotaCDController extends Controller
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
        return view('notas/index', [
            'titleGlobal' => 'Notas',
        ]);
    }

    public function dataTable()
    {
        $notas = NotasCD::getNotas();
        //cuando viene un solo objeto
        if (is_object($notas)) {
            $notas = [$notas];
        }
        foreach ($notas as $nota) {
            $nota->fecha_emision = date('d-m-Y', strtotime($nota->fecha_emision));
        }
        // dd($notas);

        //json
        echo json_encode($notas);
        exit;
    }

    public function create()
    {
        $data = $this->request()->getInput();
        if (empty($data->id)) {
            $data->id = 'vacio';
        }
        return view('notas/notaid', [
            'id' => $data->id,
            'titleGlobal' => 'Notas',
        ]);
    }

    public function venta()
    {
        $data = $this->request()->getInput();
        $venta = Ventas::getVentaNota($data->id);
        echo json_encode($venta);
        exit;
    }
    public function BuscarVenta()
    {
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Ventas::search($data->search);

        if (is_object($response)) {
            $response = [$response];
        }
        foreach ($response as $key => $value) {
            //obligatorio agregar ->textItem
            $response[$key]->textItem = $value->serie . '-' . $value->correlativo;
        }

        echo json_encode($response);
        exit;
    }

    public function tipocomprobante()
    {
        $tipoDoc = TipoComprobante::getTipoComprobante('nota');
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

    public function tiponota()
    {
        $data = $this->request()->getInput();
        $tipoNotas = TablaParametrica::getMotivos($data->nota);
        //cuando viene un solo objeto
        if (is_object($tipoNotas)) {
            $tipoNotas = [$tipoNotas];
        }
        //json
        echo json_encode($tipoNotas);
        exit;
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'usuario_id' => 'required',
            'codmotivo' => 'required',
            'descripcion' => 'required',
            'productos' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = NotasCD::create($data);
            //     ["status"]=>
            //     bool(true)
            //     ["id"]=>
            //     string(1) "1"
            //   }
            //buscar  result->id
            $nota = NotasCD::find($result->id);
            //traer SerieCorrelativo
            $serie = SerieCorrelativo::find($nota->serie_id);
            //actualizar correlativo
            $dataSerie = ['correlativo' => $serie->correlativo + 1];
            $ff = SerieCorrelativo::update($serie->id, $dataSerie);

            //crear xml y firmar
            $estado = $this->generarXML($nota->id);

            if ($estado->success) {
                //modificar el estado de la venta
                $estadoVenta = ['estado' => 0];
                Ventas::update($nota->venta_id, $estadoVenta);

                $response = ['status' => true, 'id' => $result->id, 'Message' => $estado->Message];
                // $response = ['status' => true, 'id' => $result->id];
            } else {
                $response = ['status' => false, 'Message' => $estado->Message];
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        //traer la venta
        $venta = Ventas::first($data->id);
        //traer el tipo comprobante
        $tipoComprobante = TipoComprobante::where('codigo', '07')->get();
        //traer serie y correlativo
        $seriemm = SerieCorrelativo::getSerieCorrelativo($tipoComprobante->codigo);
        if (is_object($seriemm)) {
            $seriemm = [$seriemm];
        }
        //filtar por tipodoc y traer el primer elemento
        // $serie = array_filter($serie, function ($v) use ($venta) {
        //     return $v->tipo == $venta->tipodoc;
        // });
        foreach ($seriemm as $k => $v) {
            if ($v->tipo == $venta->tipodoc) {
                $serieCorrelativo = $v;
            }
        }

        //array vacio para generarar NOTA DE CREDITO
        $notaCredito = [];
        //almacenar en $notaCredito
        $notaCredito['usuario_id'] = session()->user()->id;
        $notaCredito['venta_id'] = $venta->id;
        $notaCredito['tipodoc'] = $tipoComprobante->codigo;
        $notaCredito['nombre_tipodoc'] = $tipoComprobante->descripcion;
        $notaCredito['serie_id'] = $serieCorrelativo->id;
        $notaCredito['serie'] = $serieCorrelativo->serie;
        $notaCredito['correlativo'] = $serieCorrelativo->correlativo;
        // $notaCredito['codmotivo'] = "01";
        $notaCredito['codmotivo'] = "06";
        $notaCredito['motivo'] = "Anulación de la operación";
        // $notaCredito['descripcion'] = "devolucion de productos";
        $notaCredito['descripcion'] = "Anulación";
        $notaCredito['moneda'] = $venta->moneda;
        $notaCredito['fecha_emision'] = date('Y-m-d H:i:s');
        $notaCredito['op_gratuitas'] = $venta->op_gratuitas;
        $notaCredito['op_exoneradas'] = $venta->op_exoneradas;
        $notaCredito['op_inafectas'] = $venta->op_inafectas;
        $notaCredito['op_gravadas'] = $venta->op_gravadas;
        $notaCredito['igv_gratuita'] = $venta->igv_gratuita;
        $notaCredito['igv_exonerada'] = $venta->igv_exonerada;
        $notaCredito['igv_inafecta'] = $venta->igv_inafecta;
        $notaCredito['igv_grabada'] = $venta->igv_grabada;
        $notaCredito['igv_total'] = $venta->igv_total;
        $notaCredito['total'] = $venta->total;
        $notaCredito['cliente_id'] = $venta->cliente_id;
        $notaCredito['forma_pago'] = $venta->forma_pago;
        $notaCredito['cuotas'] = $venta->cuotas;
        $notaCredito['productos'] = $venta->productos;
        $notaCredito['tipodoc_ref'] = $venta->tipodoc;
        $notaCredito['serie_ref'] = $venta->serie;
        $notaCredito['correlativo_ref'] = $venta->correlativo;

        //crear nota de credito
        $result = NotasCD::create($notaCredito);
        //traer la nota creada
        $nota = NotasCD::find($result->id);
        //traer SerieCorrelativo
        $serie = SerieCorrelativo::find($nota->serie_id);
        //actualizar correlativo
        $dataSerie = ['correlativo' => $serie->correlativo + 1];
        $ff = SerieCorrelativo::update($serie->id, $dataSerie);

        //crear xml y firmar
        $estado = $this->generarXML($nota->id);

        if ($estado->success) {
            //modificar el estado de la venta
            $estadoVenta = ['estado' => 0];
            Ventas::update($nota->venta_id, $estadoVenta);

            $response = ['status' => true, 'id' => $result->id, 'Message' => $estado->Message];
            // $response = ['status' => true, 'id' => $result->id];
        } else {
            $response = ['status' => false, 'Message' => $estado->Message];
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function generarXML($id)
    {
        // $id = 1;
        $emisor = InfoEmpresa::getEmpresa();
        $nota = NotasCD::getNota($id);

        $cliente = Clientes::getCliente($nota->cliente_id);

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

        $detalle = json_decode($nota->productos);
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
        $nota->productos = json_encode($detalle);
        $nota->fecha_emision = date('Y-m-d', strtotime($nota->fecha_emision));

        //eviar al generador de xml y firmar
        $xml = new GeneradorXml();
        $result = $xml->xml($emisor, $nota, $cliente);

        if ($result->success) {
            $nombreXML = $emisor->ruc . '-' . $nota->tipodoc . '-' . $nota->serie . '-' . $nota->correlativo;

            $data = ['nombre_xml' => $nombreXML];
            $resp = NotasCD::update($nota->id, $data);

            if ($resp->status) {
                $notaXml = NotasCD::getNombreXML($nota->id);
                //enviar a sunat
                $enviar = new EnviarSunat();
                $result = $enviar->enviarComprobante($emisor, $notaXml->nombre_xml);
                // dd($result);
                if ($result['success']) {
                    $mmm = ['estado_sunat' => '1'];
                    $estado = NotasCD::update($nota->id, $mmm);

                    $return = ['success' => true, 'Message' => 'Se genero el XML y se envio a la SUNAT correctamente', 'nombre_xml' => $nombreXML];
                    return (object)$return;
                }
                return (object)$result;
            }
        }

        return $result;
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



    public function reporte()
    {
        $data = $this->request()->getInput();

        $emisor = InfoEmpresa::first();
        $print = new PrintPdf;

        if (isset($data->pdfA5)) {
            $nota = NotasCD::getNota($data->pdfA5);
            $cliente = Clientes::getCliente($nota->cliente_id);
            $result = $print->ModeloA5($emisor, $nota, $cliente);
            return;
        }

        if (isset($data->pdfA4)) {
            $nota = NotasCD::getNota($data->pdfA4);
            $cliente = Clientes::getCliente($nota->cliente_id);
            $result = $print->ModeloA4($emisor, $nota, $cliente);
            return;
        }

        if (isset($data->ticket)) {
            $nota = NotasCD::getNota($data->ticket);
            $cliente = Clientes::getCliente($nota->cliente_id);
            $result = $print->ModeloTicket($emisor, $nota, $cliente);
            return;
        }

        echo 'error al generar el reporte';
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
}
