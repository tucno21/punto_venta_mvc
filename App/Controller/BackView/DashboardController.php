<?php

namespace App\Controller\BackView;

use App\Model\Users;
use App\Model\Ventas;
use App\Model\Compras;
use System\Controller;
use App\Model\Clientes;
use App\Model\Productos;
use App\Model\NotaVentas;
use App\Model\Proveedores;
use App\Model\ProductosVentasTop;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class DashboardController extends Controller
{
    public function __construct()
    {
        // enviar los datos de la sesion, y los parametros de la url para validar
        // $this->except(['users', 'users.create'])->middleware('loco');
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard/index', [
            'titleGlobal' => 'Punto de venta',
        ]);
    }

    public function cantidades()
    {
        $usuarios = Users::get();
        $clientes = Clientes::get();
        $proveedores = Proveedores::get();
        $productos = Productos::get();
        $data = [
            'usuarios' => count($usuarios),
            'clientes' => count($clientes),
            'proveedores' => count($proveedores),
            'productos' => count($productos)
        ];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function ventacompra()
    {
        $ventas = Ventas::ventaTotalPorMes();
        $notaVentas = NotaVentas::ventaTotalPorMes();
        $meses = [
            'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
        ];
        foreach ($meses as $mes) {
            $ventas->$mes = $ventas->$mes + $notaVentas->$mes;
        }

        $compras = Compras::comprasTotalPorMes();
        //cambiar los valores de compras a float
        foreach ($compras as $key => $value) {
            $compras->$key = (float) $value;
        }


        $data = [
            'ventas' => $ventas,
            'compras' => $compras
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function productostock()
    {
        $productosMin = Productos::stockDebajoMinimo();
        if (is_object($productosMin)) {
            $productosMin = [$productosMin];
        }
        $productosCero = Productos::stockCero();
        if (is_object($productosCero)) {
            $productosCero = [$productosCero];
        }

        $data = [
            'productosMin' => $productosMin,
            'productosCero' => $productosCero
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function topventas()
    {
        $topVentas = ProductosVentasTop::getVentasTop();
        echo json_encode($topVentas, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function excelcompraventa()
    {
        $ventas = Ventas::ventaTotalPorMes();
        $notaVentas = NotaVentas::ventaTotalPorMes();
        $meses = [
            'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
        ];
        foreach ($meses as $mes) {
            $ventas->$mes = $ventas->$mes + $notaVentas->$mes;
        }

        $compras = Compras::comprasTotalPorMes();
        //cambiar los valores de compras a float
        foreach ($compras as $key => $value) {
            $compras->$key = (float) $value;
        }


        $data = [
            'ventas' => $ventas,
            'compras' => $compras
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte Compras y Ventas");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("reporte");
        $hojaActiva->getColumnDimension('A')->setWidth(10); //mes
        $hojaActiva->getColumnDimension('B')->setWidth(20); //ventas
        $hojaActiva->getColumnDimension('C')->setWidth(20); //compras

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:C1');
        $hojaActiva->setCellValue('A1', 'Reporte de compras y ventas');
        $hojaActiva->getStyle('A1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1')->getFont()->setSize(16);

        //estilo cabecera con bordes
        $hojaActiva->getStyle('A3:C3')->getFont()->setBold(true);
        $hojaActiva->getStyle('A3:C3')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A3:C3')->getFill()->setFillType('solid');
        $hojaActiva->getStyle('A3:C3')->getFill()->getStartColor()->setARGB('FF808080');
        $hojaActiva->getStyle('A3:C3')->getBorders()->getAllBorders()->setBorderStyle('thin');

        //cabecera
        $hojaActiva->setCellValue('A3', 'Mes');
        $hojaActiva->setCellValue('B3', 'Ventas');
        $hojaActiva->setCellValue('C3', 'Compras');

        //contenido
        $fila = 4;
        foreach ($meses as $mes) {
            $hojaActiva->setCellValue('A' . $fila, $mes);
            $hojaActiva->setCellValue('B' . $fila, $data['ventas']->$mes);
            $hojaActiva->setCellValue('C' . $fila, $data['compras']->$mes);
            //bordes
            $hojaActiva->getStyle('A' . $fila . ':C' . $fila)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $fila++;
        }

        $filename = 'CompraVentas-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    public function exceltopventas()
    {
        $topVentas = ProductosVentasTop::getVentasTop();
        if (is_object($topVentas)) {
            $topVentas = [$topVentas];
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte Top Ventas");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Top Ventas");
        $hojaActiva->getColumnDimension('A')->setWidth(10); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(20); //producto
        $hojaActiva->getColumnDimension('C')->setWidth(20); //cantidad

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:C1');
        $hojaActiva->setCellValue('A1', 'Reporte de Top Ventas');
        $hojaActiva->getStyle('A1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1')->getFont()->setSize(16);

        //estilo cabecera con bordes
        $hojaActiva->getStyle('A3:C3')->getFont()->setBold(true);
        $hojaActiva->getStyle('A3:C3')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A3:C3')->getFill()->setFillType('solid');
        $hojaActiva->getStyle('A3:C3')->getFill()->getStartColor()->setARGB('FF808080');
        $hojaActiva->getStyle('A3:C3')->getBorders()->getAllBorders()->setBorderStyle('thin');

        //cabecera
        $hojaActiva->setCellValue('A3', 'N°');
        $hojaActiva->setCellValue('B3', 'Producto');
        $hojaActiva->setCellValue('C3', 'Cant Ventas');

        //contenido
        $fila = 4;
        foreach ($topVentas as $top) {
            $hojaActiva->setCellValue('A' . $fila, $fila - 3);
            $hojaActiva->setCellValue('B' . $fila, $top->detalle);
            $hojaActiva->setCellValue('C' . $fila, $top->cant_ventas);
            //bordes
            $hojaActiva->getStyle('A' . $fila . ':C' . $fila)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $fila++;
        }

        $filename = 'topVentas-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    public function excelstockmin()
    {
        $productosMin = Productos::stockDebajoMinimo();
        if (is_object($productosMin)) {
            $productosMin = [$productosMin];
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte Productos con Stock Minimo");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Pro Min");
        $hojaActiva->getColumnDimension('A')->setWidth(10); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(25); //codigo
        $hojaActiva->getColumnDimension('C')->setWidth(30); //detalle
        $hojaActiva->getColumnDimension('D')->setWidth(15); //stock min
        $hojaActiva->getColumnDimension('E')->setWidth(15); //stock actual

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:E1');
        $hojaActiva->setCellValue('A1', 'Reporte de Productos con Stock Minimo');
        $hojaActiva->getStyle('A1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1')->getFont()->setSize(16);

        //estilo cabecera con bordes
        $hojaActiva->getStyle('A3:E3')->getFont()->setBold(true);
        $hojaActiva->getStyle('A3:E3')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A3:E3')->getFill()->setFillType('solid');
        $hojaActiva->getStyle('A3:E3')->getFill()->getStartColor()->setARGB('FF808080');
        $hojaActiva->getStyle('A3:E3')->getBorders()->getAllBorders()->setBorderStyle('thin');

        //cabecera
        $hojaActiva->setCellValue('A3', 'N°');
        $hojaActiva->setCellValue('B3', 'Codigo');
        $hojaActiva->setCellValue('C3', 'Detalle');
        $hojaActiva->setCellValue('D3', 'Stock Min');
        $hojaActiva->setCellValue('E3', 'Stock Actual');

        //contenido
        $fila = 4;
        foreach ($productosMin as $producto) {
            $hojaActiva->setCellValue('A' . $fila, $fila - 3);
            $hojaActiva->setCellValue('B' . $fila, $producto->codigo);
            $hojaActiva->setCellValue('C' . $fila, $producto->detalle);
            $hojaActiva->setCellValue('D' . $fila, $producto->stock_minimo);
            $hojaActiva->setCellValue('E' . $fila, $producto->stock);
            //bordes
            $hojaActiva->getStyle('A' . $fila . ':E' . $fila)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $fila++;
        }

        $filename = 'StockMin-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    public function excelstockcero()
    {
        $productosCero = Productos::stockCero();
        if (is_object($productosCero)) {
            $productosCero = [$productosCero];
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte Productos con Stock Cero");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Prod Cero");
        $hojaActiva->getColumnDimension('A')->setWidth(10); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(25); //codigo
        $hojaActiva->getColumnDimension('C')->setWidth(30); //detalle
        $hojaActiva->getColumnDimension('D')->setWidth(15); //stock min
        $hojaActiva->getColumnDimension('E')->setWidth(15); //stock actual

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:E1');
        $hojaActiva->setCellValue('A1', 'Reporte de Productos con Stock Cero');
        $hojaActiva->getStyle('A1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1')->getFont()->setSize(16);

        //estilo cabecera con bordes
        $hojaActiva->getStyle('A3:E3')->getFont()->setBold(true);
        $hojaActiva->getStyle('A3:E3')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A3:E3')->getFill()->setFillType('solid');
        $hojaActiva->getStyle('A3:E3')->getFill()->getStartColor()->setARGB('FF808080');
        $hojaActiva->getStyle('A3:E3')->getBorders()->getAllBorders()->setBorderStyle('thin');

        //cabecera
        $hojaActiva->setCellValue('A3', 'N°');
        $hojaActiva->setCellValue('B3', 'Codigo');
        $hojaActiva->setCellValue('C3', 'Detalle');
        $hojaActiva->setCellValue('D3', 'Stock Min');
        $hojaActiva->setCellValue('E3', 'Stock Actual');

        //contenido
        $fila = 4;
        foreach ($productosCero as $producto) {
            $hojaActiva->setCellValue('A' . $fila, $fila - 3);
            $hojaActiva->setCellValue('B' . $fila, $producto->codigo);
            $hojaActiva->setCellValue('C' . $fila, $producto->detalle);
            $hojaActiva->setCellValue('D' . $fila, $producto->stock_minimo);
            $hojaActiva->setCellValue('E' . $fila, $producto->stock);
            //bordes
            $hojaActiva->getStyle('A' . $fila . ':E' . $fila)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $fila++;
        }

        $filename = 'StockCero-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
