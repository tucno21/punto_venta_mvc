<?php

namespace App\Controller\BackView;

use App\Model\Ventas;
use System\Controller;
use App\Model\Clientes;
use App\Model\Productos;
use App\Model\NotaVentas;
use App\Library\FPDF\FPDF;
use App\Model\InfoEmpresa;
use App\Model\Inventarios;
use App\Model\Factura\Monedas;
use App\Help\PrintPdf\PrintPdf;
use App\Model\ProductosVentasTop;
use App\Model\Factura\TipoComprobante;
use App\Model\Factura\SerieCorrelativo;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
            // $response = $this->guardarVenta($data);
            // echo json_encode($response, JSON_UNESCAPED_UNICODE);
            // exit;
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
                $productos =  json_decode($venta->productos);
                foreach ($productos as $producto) {
                    //actualiza el stock de los productos
                    //buscar el producto
                    $pro = Productos::where('id', $producto->id)->first();

                    $actualizar = [
                        'stock' => $pro->stock - $producto->cantidad,
                    ];

                    Productos::update($producto->id, $actualizar);

                    //regitrar en el inventario
                    $inventario = [
                        'producto_id' => $producto->id,
                        'comprobante' => $venta->serie . '-' . $venta->correlativo,
                        'cantidad' => $producto->cantidad,
                        'fecha' => $venta->fecha_emision,
                        'tipo' => 'salida',
                        'accion' => 'Venta',
                        'stock_actual' => $pro->stock - $producto->cantidad,
                        'user_id' => $venta->usuario_id,
                    ];
                    Inventarios::create($inventario);

                    //productos top ventas
                    $topVentas = ProductosVentasTop::getProducto($producto->id);
                    //si el array esta vacio
                    if (empty($topVentas)) {
                        $topVentas = [
                            'producto_id' => $producto->id,
                            'cant_ventas' => $producto->cantidad,
                        ];
                        ProductosVentasTop::registrar((object)$topVentas);
                    } else {
                        $topVentas = [
                            'cant_ventas' => $topVentas->cant_ventas + $producto->cantidad,
                        ];
                        ProductosVentasTop::actualizar($producto->id, (object)$topVentas);
                    }
                }


                $response = ['status' => true, 'id' => $result->id, 'message' => $estado->message];
            } else {
                $response = ['status' => false, 'message' => $estado->message];
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function guardarVenta($data)
    {
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
            $response = ['status' => true, 'id' => $result->id, 'message' => $estado->message];
        } else {
            $response = ['status' => false, 'message' => $estado->message];
        }
        return $response;
    }

    public function generarXML($id)
    {
        // $id = 35;
        $emisor = InfoEmpresa::getEmpresa();

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
                $return = ['success' => true, 'message' => 'Se genero el XML correctamente', 'nombre_xml' => $nombreXML];
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

    public function boleta()
    {
        $data = $this->request()->getInput();
        //traer notas de venta
        $venta = NotaVentas::getVentaId($data->id);
        //
        //buscar inventario
        $inventario = Inventarios::getInventarioComprobante($venta->serie . "-" . $venta->correlativo);

        $tipoComprobante = TipoComprobante::where('codigo', '03')->get();

        $seriemm = SerieCorrelativo::getSerieCorrelativo($tipoComprobante->codigo);
        if (is_object($seriemm)) {
            $seriemm = [$seriemm];
        }

        //cambios
        $venta->tipodoc = $tipoComprobante->codigo;
        $venta->nombre_tipodoc = $tipoComprobante->descripcion;
        $venta->serie_id = $seriemm[0]->id;
        $venta->serie = $seriemm[0]->serie;
        $venta->correlativo = $seriemm[0]->correlativo;
        $venta->fecha_emision = date('Y-m-d H:i:s');
        $venta->cuotas = "";
        //eliminar $venta->estado_sunat
        unset($venta->estado_sunat);
        unset($venta->estado);
        unset($venta->created_at);
        unset($venta->updated_at);
        unset($venta->id);

        //actualizar el inventario
        Inventarios::update($inventario->id, [
            'comprobante' => $venta->serie . "-" . $venta->correlativo,
            'fecha' => $venta->fecha_emision,
        ]);

        $response = $this->guardarVenta($venta);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function factura()
    {
        $data = $this->request()->getInput();
        //traer notas de venta
        $venta = NotaVentas::getVentaId($data->id);
        //
        //buscar inventario
        $inventario = Inventarios::getInventarioComprobante($venta->serie . "-" . $venta->correlativo);

        $tipoComprobante = TipoComprobante::where('codigo', '01')->get();

        $seriemm = SerieCorrelativo::getSerieCorrelativo($tipoComprobante->codigo);
        if (is_object($seriemm)) {
            $seriemm = [$seriemm];
        }

        //cambios
        $venta->tipodoc = $tipoComprobante->codigo;
        $venta->nombre_tipodoc = $tipoComprobante->descripcion;
        $venta->serie_id = $seriemm[0]->id;
        $venta->serie = $seriemm[0]->serie;
        $venta->correlativo = $seriemm[0]->correlativo;
        $venta->fecha_emision = date('Y-m-d H:i:s');
        $venta->cuotas = "";
        //eliminar $venta->estado_sunat
        unset($venta->estado_sunat);
        unset($venta->estado);
        unset($venta->created_at);
        unset($venta->updated_at);
        unset($venta->id);

        //actualizar el inventario
        Inventarios::update($inventario->id, [
            'comprobante' => $venta->serie . "-" . $venta->correlativo,
            'fecha' => $venta->fecha_emision,
        ]);

        $response = $this->guardarVenta($venta);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function ventaspdf()
    {
        $data = $this->request()->getInput();
        $ventas = Ventas::getVentasFechas($data->fecha_inicio, $data->fecha_fin);
        if (is_object($ventas)) {
            $ventas = [$ventas];
        }
        //$ventas si es un array vacio
        if (empty($ventas)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Reporte de Ventas');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Reporte: Ventas', 0, 1, 'C');
        $pdf->Ln(5);

        //subtitulo fecha_inicio - fecha_fin
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, 'Fecha Inicio: ' . $data->fecha_inicio . ' - Fecha Fin: ' . $data->fecha_fin, 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetAutoPageBreak('auto', 2); // 2 es el margen inferior
        $pdf->SetDisplayMode(75); // zoom 75% (opcional)

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(8, 5, utf8_decode('N°'), 1, 0, 'C');
        $pdf->Cell(25, 5, utf8_decode('Comprobante'), 1, 0, 'C');
        $pdf->Cell(35, 5, utf8_decode('Fecha Emision'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('Total'), 1, 0, 'C');
        $pdf->Cell(50, 5, utf8_decode('Cliente'), 1, 0, 'C');
        $pdf->Cell(25, 5, utf8_decode('Vendedor'), 1, 0, 'C');
        $pdf->Cell(30, 5, utf8_decode('Sunat'), 1, 0, 'C');
        $pdf->Ln(5);

        $i = 1;
        foreach ($ventas as $venta) {
            if ($venta->estado == 1 && $venta->estado_sunat == 1)
                $venta->condicion = 'Aceptado';
            if ($venta->estado == 1 && $venta->estado_sunat == 0)
                $venta->condicion = 'sin enviar';
            if ($venta->estado == 0)
                $venta->condicion = 'Venta anulada';

            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(8, 5, $i, 1, 0, 'C');
            $pdf->Cell(25, 5, $venta->serie . '-' . $venta->correlativo, 1, 0, 'L');
            $pdf->Cell(35, 5, $venta->fecha_emision, 1, 0, 'C');
            $pdf->Cell(20, 5, $venta->total, 1, 0, 'R');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(50, 5, $venta->cliente, 1, 0, 'L');
            $pdf->Cell(25, 5, $venta->vendedor, 1, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(30, 5, $venta->condicion, 1, 0, 'C');
            $pdf->Ln(5);
            $i++;
        }

        $pdf->Output("Reporte-ventas" . ".pdf", "I");
    }

    public function ventasexcel()
    {
        $data = $this->request()->getInput();
        $ventas = Ventas::getVentasFechas($data->fecha_inicio, $data->fecha_fin);
        if (is_object($ventas)) {
            $ventas = [$ventas];
        }
        //$ventas si es un array vacio
        if (empty($ventas)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte de Ventas");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Ventas");
        $hojaActiva->getColumnDimension('A')->setWidth(5); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(20); //comprobante
        $hojaActiva->getColumnDimension('C')->setWidth(30); //fecha de emision
        $hojaActiva->getColumnDimension('D')->setWidth(10); //total
        $hojaActiva->getColumnDimension('E')->setWidth(45); //cliente
        $hojaActiva->getColumnDimension('F')->setWidth(35); //vendedor
        $hojaActiva->getColumnDimension('G')->setWidth(25); //estado

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:G1');
        $hojaActiva->mergeCells('A2:G2');
        $hojaActiva->mergeCells('A3:G3');
        //centrar titulo
        $hojaActiva->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A2:G2')->getAlignment()->setHorizontal('center');
        //font
        $hojaActiva->getStyle('A1:G1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:G1')->getFont()->setSize(16);

        //titulo
        $hojaActiva->setCellValue('A1', 'Reporte: Ventas');
        $hojaActiva->setCellValue('A2', 'Fecha Inicio: ' . $data->fecha_inicio . ' - Fecha Fin: ' . $data->fecha_fin);
        $hojaActiva->setCellValue('A3', 'Fecha de emision: ' . date('Y-m-d H:i:s'));

        //cabecera
        $hojaActiva->setCellValue('A5', 'N°');
        $hojaActiva->setCellValue('B5', 'Comprobante');
        $hojaActiva->setCellValue('C5', 'Fecha Emision');
        $hojaActiva->setCellValue('D5', 'Total');
        $hojaActiva->setCellValue('E5', 'Cliente');
        $hojaActiva->setCellValue('F5', 'Vendedor');
        $hojaActiva->setCellValue('G5', 'Estado');
        //centrar
        $hojaActiva->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A5:G5')->getFont()->setBold(true);
        $hojaActiva->getStyle('A5:G5')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $i = 1;
        foreach ($ventas as $venta) {
            if ($venta->estado == 1 && $venta->estado_sunat == 1)
                $venta->condicion = 'Aceptado';
            if ($venta->estado == 1 && $venta->estado_sunat == 0)
                $venta->condicion = 'Sin enviar';
            if ($venta->estado == 0)
                $venta->condicion = 'Venta anulada';

            $hojaActiva->setCellValue('A' . ($i + 5), $i);
            $hojaActiva->setCellValue('B' . ($i + 5), $venta->serie . '-' . $venta->correlativo);
            $hojaActiva->setCellValue('C' . ($i + 5), $venta->fecha_emision);
            $hojaActiva->setCellValue('D' . ($i + 5), $venta->total);
            $hojaActiva->setCellValue('E' . ($i + 5), $venta->cliente);
            $hojaActiva->setCellValue('F' . ($i + 5), $venta->vendedor);
            $hojaActiva->setCellValue('G' . ($i + 5), $venta->condicion);
            //borde
            $hojaActiva->getStyle('A' . ($i + 5) . ':G' . ($i + 5))->getBorders()->getAllBorders()->setBorderStyle('thin');
            $i++;
        }

        $filename = 'ventas-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
