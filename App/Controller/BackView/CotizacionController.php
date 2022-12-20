<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Clientes;
use App\Model\Productos;
use App\Model\NotaVentas;
use App\Library\FPDF\FPDF;
use App\Model\InfoEmpresa;
use App\Model\Inventarios;
use App\Model\Cotizaciones;
use App\Model\Factura\Monedas;
use App\Help\PrintPdf\PrintPdf;
use App\Model\ProductosVentasTop;
use App\Model\Factura\TipoComprobante;
use App\Model\Factura\SerieCorrelativo;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CotizacionController extends Controller
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
        return view('cotizaciones/index', [
            'titleGlobal' => 'Cotizaciones',
        ]);
    }

    public function dataTable()
    {
        $cotizaciones = Cotizaciones::getCotizaciones();
        //cuando viene un solo objeto
        if (is_object($cotizaciones)) {
            $cotizaciones = [$cotizaciones];
        }
        // foreach ($cotizaciones as $coti) {
        //     $coti->fecha_emision = date('d-m-Y', strtotime($coti->fecha_emision));
        // }
        // dd($cotizaciones);

        //json
        echo json_encode($cotizaciones);
        exit;
    }

    public function create()
    {
        return view('cotizaciones/cotizacion', [
            'titleGlobal' => 'Crear Cotizacion',
        ]);
    }

    public function tipocomprobante()
    {
        $tipoDoc = TipoComprobante::getTipoComprobante('cotizacion');
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
            // dd($data);
            $result = Cotizaciones::create($data);
            //     ["status"]=>
            //     bool(true)
            //     ["id"]=>
            //     string(1) "1"
            //   }
            //buscar  result->id
            $coti = Cotizaciones::find($result->id);
            //traer SerieCorrelativo
            $serie = SerieCorrelativo::find($coti->serie_id);
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
        // $result = NotaVentas::delete((int)$data->id);
        $notaVentas = Cotizaciones::select('id', 'estado', 'productos', 'serie', 'correlativo')->where('id', $data->id)->get();

        // $mmm = ['estado' => '0'];
        // $result = NotaVentas::update($data->id, $mmm);
        $estado = ($notaVentas->estado == 1) ? 0 : 1;
        $result = Cotizaciones::update($data->id, ['estado' => $estado]);

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
            $cotizacion = Cotizaciones::getCotizacion($data->pdfA5);
            $cliente = Clientes::getCliente($cotizacion->cliente_id);
            $result = $print->ModeloA5($emisor, $cotizacion, $cliente);
            return;
        }

        if (isset($data->pdfA4)) {
            $cotizacion = Cotizaciones::getCotizacion($data->pdfA4);
            $cliente = Clientes::getCliente($cotizacion->cliente_id);
            $result = $print->ModeloA4($emisor, $cotizacion, $cliente);
            return;
        }

        if (isset($data->ticket)) {
            $cotizacion = Cotizaciones::getCotizacion($data->ticket);
            $cliente = Clientes::getCliente($cotizacion->cliente_id);
            $result = $print->ModeloTicket($emisor, $cotizacion, $cliente);
            return;
        }

        echo 'error al generar el reporte';
        exit;
    }

    public function updateElectronico()
    {
        $data = $this->request()->getInput();
        //existe $data->id
        if (isset($data->venta_id)) {
            $mmm = ['estado_sunat' => '1', 'venta_id' => $data->venta_id];
        }
        if (isset($data->ventainterna_id)) {
            $mmm = ['estado_sunat' => '2', 'venta_id' => $data->ventainterna_id];
        }
        $estado = Cotizaciones::update($data->id, $mmm);
        $response = ['status' => true, 'message' => 'se actualizo correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function cotizacionespdf()
    {
        $data = $this->request()->getInput();
        $ventas = Cotizaciones::getCotizacionesFechas($data->fecha_inicio, $data->fecha_fin);
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
        $pdf->setTitle('Reporte de Notas de Ventas');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Reporte: Notas de Ventas', 0, 1, 'C');
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
        $pdf->Cell(30, 5, utf8_decode('Estado'), 1, 0, 'C');
        $pdf->Ln(5);

        $i = 1;
        foreach ($ventas as $venta) {
            if ($venta->estado == 1 && $venta->estado_sunat == 1)
                $venta->condicion = 'Genero XML';
            if ($venta->estado == 1 && $venta->estado_sunat == 0)
                $venta->condicion = 'Venta interna';
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

    public function cotizacionesexcel()
    {
        $data = $this->request()->getInput();
        $ventas = Cotizaciones::getCotizacionesFechas($data->fecha_inicio, $data->fecha_fin);
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
        $spreadsheet->getProperties()->setTitle("Reporte de Notas de Ventas");

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
        $hojaActiva->setCellValue('A1', 'Reporte: Notas de Ventas');
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
                $venta->condicion = 'Genero XML';
            if ($venta->estado == 1 && $venta->estado_sunat == 0)
                $venta->condicion = 'Venta interna';
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

    public function ventainterna()
    {
        $data = $this->request()->getInput();
        $cotizacion = Cotizaciones::getCotizacionId($data->id);

        $tipoComprobante = TipoComprobante::where('codigo', '20')->get();

        $seriemm = SerieCorrelativo::getSerieCorrelativo($tipoComprobante->codigo);
        if (is_object($seriemm)) {
            $seriemm = [$seriemm];
        }

        //cambios
        $cotizacion->tipodoc = $tipoComprobante->codigo;
        $cotizacion->nombre_tipodoc = $tipoComprobante->descripcion;
        $cotizacion->serie_id = $seriemm[0]->id;
        $cotizacion->serie = $seriemm[0]->serie;
        $cotizacion->correlativo = $seriemm[0]->correlativo;
        $cotizacion->fecha_emision = date('Y-m-d H:i:s');
        $cotizacion->cuotas = "";
        //eliminar $cotizacion->estado_sunat
        unset($cotizacion->estado_sunat);
        unset($cotizacion->estado);
        unset($cotizacion->created_at);
        unset($cotizacion->updated_at);
        unset($cotizacion->id);
        unset($cotizacion->venta_id);
        unset($cotizacion->tiempo);

        $result = NotaVentas::create($cotizacion);
        $venta = NotaVentas::find($result->id);
        //traer SerieCorrelativo
        $serie = SerieCorrelativo::find($venta->serie_id);
        //actualizar correlativo
        $dataSerie = ['correlativo' => $serie->correlativo + 1];
        $ff = SerieCorrelativo::update($serie->id, $dataSerie);

        $productos =  json_decode($venta->productos);
        foreach ($productos as $producto) {
            //actualiza el stock de los productos
            //buscar el producto
            $pro = Productos::getProd($producto->id);

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


        $response = ['status' => true, 'id' => $result->id];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
