<?php

namespace App\Controller\BackView;

use App\Model\Compras;
use System\Controller;
use App\Model\Productos;
use App\Library\FPDF\FPDF;
use App\Model\Inventarios;
use App\Model\Factura\TipoComprobante;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
            //traer la compra
            $compra = Compras::find($result->id);

            // $productos =  json_decode($compra->productos, true);//true para que sea array
            $productos =  json_decode($compra->productos);
            foreach ($productos as $producto) {
                //actualiza el stock de los productos
                //buscar el producto
                $pro = Productos::where('id', $producto->id)->first();

                $actualizar = [
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $producto->precio_venta,
                    'stock' => $pro->stock + $producto->cantidad,
                ];

                Productos::update($producto->id, $actualizar);

                //regitrar en el inventario
                $inventario = [
                    'producto_id' => $producto->id,
                    'comprobante' => $compra->serie,
                    'cantidad' => $producto->cantidad,
                    'fecha' => $compra->fecha_compra,
                    'tipo' => 'entrada',
                    'accion' => 'Compra',
                    'stock_actual' => $pro->stock + $producto->cantidad,
                    'user_id' => $compra->user_id,
                ];
                Inventarios::create($inventario);
            }


            $response = ['status' => true, 'data' => 'Creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $compra = Compras::select('id', 'estado', 'productos', 'serie')->where('id', $data->id)->get();

        $productos =  json_decode($compra->productos);
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
                'comprobante' => $compra->serie,
                'cantidad' => $producto->cantidad,
                'fecha' => date('Y-m-d H:i:s'),
                'tipo' => 'salida',
                'accion' => 'C. Anulada',
                'stock_actual' => $pro->stock - $producto->cantidad,
                'user_id' => session()->user()->id,
            ];
            Inventarios::create($inventario);
        }


        // dd($user);
        $estado = ($compra->estado == 1) ? 0 : 1;
        $result = Compras::update($data->id, ['estado' => $estado]);
        $response = ['status' => true, 'data' => 'Anulado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function reporte()
    {
        $result = $this->request()->isGet();
        if ($result) {
            $data = $this->request()->getInput();
            $compra = Compras::getCompra($data->id);

            $compra->fecha_compra = date('d-m-Y', strtotime($compra->fecha_compra));

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->setMargins(10, 10, 10);
            $pdf->setTitle('Detalle de Compra');
            $pdf->SetFont('Arial', 'B', 12);

            $pdf->Cell(190, 5, 'Entrada de Productos', 0, 1, 'C');
            if ($compra->estado == 0) {
                $pdf->Ln();
                $pdf->Cell(190, 5, 'ANULADO', 0, 1, 'C');
            }

            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell(30, 5, 'Comprobante ', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(10, 5,  ': ' . $compra->serie, 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(30, 5, 'Proveedor ', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(10, 5,  ': ' . $compra->proveedor, 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(30, 5, 'Fecha ', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(10, 5,  ': ' . $compra->fecha_compra, 0, 1, 'L');

            $pdf->Ln();
            $pdf->Ln();

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(190, 7, 'Detalle de Compra', 1, 1, 'C', true);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(10, 8, utf8_decode('N°'), 1, 0, 'L');
            $pdf->Cell(35, 8, utf8_decode('Código'), 1, 0, 'L');
            $pdf->Cell(63, 8, 'Nombre', 1, 0, 'L');
            $pdf->Cell(26, 8, 'Precio', 1, 0, 'L');
            $pdf->Cell(26, 8, 'Cantidad', 1, 0, 'L');
            $pdf->Cell(30, 8, 'Importe', 1, 1, 'L');

            $pdf->SetFont('Arial', '', 12);


            $productos = json_decode($compra->productos);

            $i = 1;
            foreach ($productos as $value) {
                $pdf->Cell(10, 8, $i, 1, 0, 'L');
                $pdf->Cell(35, 8, utf8_decode($value->codigo), 1, 0, 'L');
                $pdf->Cell(63, 8, utf8_decode($value->detalle), 1, 0, 'L');
                $pdf->Cell(26, 8, 'S/ ' . number_format($value->precio_compra, 2, '.', ''), 1, 0, 'L');
                $pdf->Cell(26, 8, $value->cantidad, 1, 0, 'L');

                $subtotal = $value->cantidad * $value->precio_compra;
                $pdf->Cell(30, 8, 'S/ ' . number_format($subtotal, 2, '.', ''), 1, 1, 'R');

                $i++;
            }

            $pdf->Cell(10, 8, '', 0, 0, 'L');
            $pdf->Cell(35, 8, '', 0, 0, 'L');
            $pdf->Cell(63, 8, '', 0, 0, 'L');
            $pdf->Cell(26, 8, '', 0, 0, 'L');
            $pdf->Cell(26, 8, 'TOTAL', 1, 0, 'L');
            $pdf->Cell(30, 8, 'S/ ' . $compra->total, 1, 1, 'R');



            $pdf->Output("Compra" . $compra->serie . "-" . $compra->fecha_compra . ".pdf", "I");
        }
    }

    public function compraspdf()
    {
        $data = $this->request()->getInput();
        $compras = Compras::getComprasFechas($data->fecha_inicio, $data->fecha_fin);
        if (is_object($compras)) {
            $compras = [$compras];
        }
        //$compras si es un array vacio
        if (empty($compras)) {
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
        $pdf->Cell(35, 5, utf8_decode('Fecha Compra'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('Total'), 1, 0, 'C');
        $pdf->Cell(75, 5, utf8_decode('Proveedor'), 1, 0, 'C');
        $pdf->Cell(25, 5, utf8_decode('Estado'), 1, 0, 'C');
        $pdf->Ln(5);

        $i = 1;
        foreach ($compras as $compra) {
            if ($compra->estado == 1)
                $compra->condicion = 'ok';
            if ($compra->estado == 0)
                $compra->condicion = 'C. anulada';

            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(8, 5, $i, 1, 0, 'C');
            $pdf->Cell(25, 5, $compra->serie, 1, 0, 'L');
            $pdf->Cell(35, 5, $compra->fecha_compra, 1, 0, 'C');
            $pdf->Cell(20, 5, $compra->total, 1, 0, 'R');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(75, 5, $compra->proveedor, 1, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(25, 5, $compra->condicion, 1, 0, 'C');
            $pdf->Ln(5);
            $i++;
        }

        $pdf->Output("Reporte-ventas" . ".pdf", "I");
    }

    public function comprasexcel()
    {
        $data = $this->request()->getInput();
        $compras = Compras::getComprasFechas($data->fecha_inicio, $data->fecha_fin);
        if (is_object($compras)) {
            $compras = [$compras];
        }
        //$compras si es un array vacio
        if (empty($compras)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte de Ventas");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Ventas");
        $hojaActiva->getColumnDimension('A')->setWidth(5); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(17); //comprobante
        $hojaActiva->getColumnDimension('C')->setWidth(25); //fecha de compra
        $hojaActiva->getColumnDimension('D')->setWidth(10); //total
        $hojaActiva->getColumnDimension('E')->setWidth(50); //proveedor
        $hojaActiva->getColumnDimension('F')->setWidth(20); //estado

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:F1');
        $hojaActiva->mergeCells('A2:F2');
        $hojaActiva->mergeCells('A3:F3');
        //centrar titulo
        $hojaActiva->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
        //font
        $hojaActiva->getStyle('A1:F1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:F1')->getFont()->setSize(16);

        //titulo
        $hojaActiva->setCellValue('A1', 'Reporte: Compras');
        $hojaActiva->setCellValue('A2', 'Fecha Inicio: ' . $data->fecha_inicio . ' - Fecha Fin: ' . $data->fecha_fin);
        $hojaActiva->setCellValue('A3', 'Fecha de emision: ' . date('Y-m-d H:i:s'));

        //cabecera
        $hojaActiva->setCellValue('A5', 'N°');
        $hojaActiva->setCellValue('B5', 'Comprobante');
        $hojaActiva->setCellValue('C5', 'Fecha Emision');
        $hojaActiva->setCellValue('D5', 'Total');
        $hojaActiva->setCellValue('E5', 'Proveedor');
        $hojaActiva->setCellValue('F5', 'Estado');
        //centrar
        $hojaActiva->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A5:G5')->getFont()->setBold(true);
        $hojaActiva->getStyle('A5:G5')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $i = 1;
        foreach ($compras as $compra) {
            if ($compra->estado == 1)
                $compra->condicion = 'ok';
            if ($compra->estado == 0)
                $compra->condicion = 'C. anulada';

            $hojaActiva->setCellValue('A' . ($i + 5), $i);
            $hojaActiva->setCellValue('B' . ($i + 5), $compra->serie);
            $hojaActiva->setCellValue('C' . ($i + 5), $compra->fecha_compra);
            $hojaActiva->setCellValue('D' . ($i + 5), $compra->total);
            $hojaActiva->setCellValue('E' . ($i + 5), $compra->proveedor);
            $hojaActiva->setCellValue('F' . ($i + 5), $compra->condicion);
            //borde
            $hojaActiva->getStyle('A' . ($i + 5) . ':F' . ($i + 5))->getBorders()->getAllBorders()->setBorderStyle('thin');
            $i++;
        }

        $filename = 'Compras-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
