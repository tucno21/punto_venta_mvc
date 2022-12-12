<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Library\FPDF\FPDF;
use App\Model\Inventarios;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InventarioController extends Controller
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
        return view('inventarios/index', [
            'titleGlobal' => 'Inventario',
        ]);
    }

    public function dataTable()
    {
        $inventarios = Inventarios::getInventarios();
        //cuando viene un solo objeto
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }
        foreach ($inventarios as $inventario) {
            $inventario->fecha = date('d-m-Y', strtotime($inventario->fecha));
        }

        //json
        echo json_encode($inventarios);
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
        //     Model::create($data);
        //     return redirect()->route('route.name');
        // }
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

    public function searchmonth()
    {
        $data = $this->request()->getInput();
        //separar el mes y el año
        $mes = explode('-', $data->mes)[1];
        $ano = explode('-', $data->mes)[0];

        $inventarios = Inventarios::getInventarioMes($mes, $ano);
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }
        foreach ($inventarios as $inventario) {
            $inventario->fecha = date('d-m-Y', strtotime($inventario->fecha));
        }

        echo json_encode($inventarios);
        exit;
    }

    public function monthpdf()
    {
        $data = $this->request()->getInput();
        $mes = explode('-', $data->mes)[1];
        $ano = explode('-', $data->mes)[0];

        $inventarios = Inventarios::getInventarioMes($mes, $ano);
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Reporte de inventario');

        //fecha de hoy de gerara el inventario
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Fecha: ' . date('d-m-Y'), 0, 1, 'R');

        //titulo
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de inventario', 0, 1, 'C');

        //mes
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Mes: ' . $data->mes, 0, 1, 'C');

        //cabecera
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(60, 10, 'Producto', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Comprobante', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Fecha', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Cantidad', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Tipo', 1, 1, 'C');

        // cuerpo
        $pdf->SetFont('Arial', '', 10);
        foreach ($inventarios as $inventario) {
            $pdf->Cell(60, 10, utf8_decode($inventario->codigo . "-" . $inventario->producto), 1, 0, 'L');
            $pdf->Cell(35, 10, $inventario->comprobante, 1, 0, 'C');
            $pdf->Cell(35, 10, date('d-m-Y', strtotime($inventario->fecha)), 1, 0, 'C');
            $pdf->Cell(25, 10, $inventario->cantidad, 1, 0, 'C');
            $pdf->Cell(30, 10, $inventario->accion, 1, 1, 'C');
        }

        $pdf->Output("Inventario-" . $mes . "-" . $ano . ".pdf", "I");
    }

    public function monthexcel()
    {
        $data = $this->request()->getInput();
        $mes = explode('-', $data->mes)[1];
        $ano = explode('-', $data->mes)[0];

        $inventarios = Inventarios::getInventarioMes($mes, $ano);
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }
        if (empty($inventarios)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte de Inventario");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Inventario");
        $hojaActiva->getColumnDimension('A')->setWidth(5); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(40); //producto
        $hojaActiva->getColumnDimension('C')->setWidth(20); //comprobante
        $hojaActiva->getColumnDimension('D')->setWidth(20); //fecha
        $hojaActiva->getColumnDimension('E')->setWidth(30); //tipo

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:E1');
        $hojaActiva->setCellValue('A1', 'Reporte de inventario');
        $hojaActiva->getStyle('A1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1')->getFont()->setSize(16);
        $hojaActiva->getStyle('A1')->getAlignment()->setHorizontal('center');

        //unir celas para el mes
        $hojaActiva->mergeCells('A2:E2');
        $hojaActiva->setCellValue('A2', 'Mes: ' . $data->mes);
        $hojaActiva->getStyle('A2')->getFont()->setBold(true);
        $hojaActiva->getStyle('A2')->getFont()->setSize(12);
        $hojaActiva->getStyle('A2')->getAlignment()->setHorizontal('center');


        //cabecera
        $hojaActiva->setCellValue('A3', 'N°');
        $hojaActiva->setCellValue('B3', 'Producto');
        $hojaActiva->setCellValue('C3', 'Comprobante');
        $hojaActiva->setCellValue('D3', 'Fecha');
        $hojaActiva->setCellValue('E3', 'Tipo');

        $hojaActiva->getStyle('A3:E3')->getFont()->setBold(true);
        $hojaActiva->getStyle('A3:E3')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A3:E3')->getAlignment()->setVertical('center');
        $hojaActiva->getStyle('A3:E3')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $i = 4;
        foreach ($inventarios as $inventario) {
            $hojaActiva->setCellValue('A' . $i, $i - 3);
            $hojaActiva->setCellValue('B' . $i, $inventario->codigo . "-" . $inventario->producto);
            $hojaActiva->setCellValue('C' . $i, $inventario->comprobante);
            $hojaActiva->setCellValue('D' . $i, date('d-m-Y', strtotime($inventario->fecha)));
            $hojaActiva->setCellValue('E' . $i, $inventario->accion);

            $hojaActiva->getStyle('A' . $i . ':E' . $i)->getAlignment()->setHorizontal('center');
            $hojaActiva->getStyle('A' . $i . ':E' . $i)->getAlignment()->setVertical('center');
            $hojaActiva->getStyle('A' . $i . ':E' . $i)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $i++;
        }

        $filename = 'Inventario-' . $mes . '-' . $ano;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function kardex()
    {
        return view('inventarios.kardex', [
            'titleGlobal' => 'Kardex',
        ]);
    }

    public function tablekardex()
    {
        $data = $this->request()->getInput();
        $inventarios = Inventarios::getKardex($data->productoid);
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }
        echo json_encode($inventarios);
        exit;
    }

    public function searchdate()
    {
        $data = $this->request()->getInput();
        $productoID = $data->productoid;
        $fechaInicio = $data->fecha_inicio;
        $fechaFin = $data->fecha_fin;

        $inventarios = Inventarios::getInventarioFecha($productoID, $fechaInicio, $fechaFin);
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }
        echo json_encode($inventarios);
        exit;
    }

    public function kardexpdf()
    {
        $data = $this->request()->getInput();

        $listaInventario;

        //existe variable fecha
        if (isset($data->fecha_inicio) && isset($data->fecha_fin)) {
            $inventarios = Inventarios::getInventarioFecha($data->productoid, $data->fecha_inicio, $data->fecha_fin);
            if (is_object($inventarios)) {
                $inventarios = [$inventarios];
            }
            $listaInventario = $inventarios;
        } else {
            $inventarios = Inventarios::getKardex($data->productoid);
            if (is_object($inventarios)) {
                $inventarios = [$inventarios];
            }
            $listaInventario = $inventarios;
        }
        //$listaInventario si es un array vacio
        if (empty($listaInventario)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Reporte de Kardex');

        //fecha de hoy de gerara el inventario
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Fecha: ' . date('d-m-Y'), 0, 1, 'R');

        //titulo
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de Kardex', 0, 1, 'C');

        //cabecera
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(8, 10, '#', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Producto', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Comprobante', 1, 0, 'C');
        $pdf->Cell(18, 10, 'Entrada', 1, 0, 'C');
        $pdf->Cell(18, 10, 'Salida', 1, 0, 'C');
        $pdf->Cell(45, 10, 'Fecha', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Saldo', 1, 1, 'C');
        // dd($listaInventario);
        // cuerpo
        $i = 1;
        $pdf->SetFont('Arial', '', 10);
        foreach ($listaInventario as $inventario) {
            if ($inventario->tipo == "entrada") {
                $inventario->entrada = $inventario->cantidad;
                $inventario->salida = 0;
            }
            if ($inventario->tipo == "salida") {
                $inventario->entrada = 0;
                $inventario->salida = $inventario->cantidad;
            }
            $pdf->Cell(8, 10, $i, 1, 0, 'C');
            $pdf->Cell(50, 10, utf8_decode($inventario->codigo . "-" . $inventario->producto), 1, 0, 'L');
            $pdf->Cell(30, 10, $inventario->comprobante, 1, 0, 'C');
            $pdf->Cell(18, 10, $inventario->entrada, 1, 0, 'C');
            $pdf->Cell(18, 10, $inventario->salida, 1, 0, 'C');
            $pdf->Cell(45, 10, date('d-m-Y', strtotime($inventario->fecha)), 1, 0, 'C');
            $pdf->Cell(25, 10, $inventario->stock_actual, 1, 1, 'C');
            $i++;
        }

        $pdf->Output("Kardex-" . $listaInventario[0]->codigo . ".pdf", "I");
    }

    public function kardexexcel()
    {
        $data = $this->request()->getInput();
        $productoID = $data->productoid;
        $fechaInicio = $data->fecha_inicio;
        $fechaFin = $data->fecha_fin;

        $inventarios = Inventarios::getInventarioFecha($productoID, $fechaInicio, $fechaFin);
        if (is_object($inventarios)) {
            $inventarios = [$inventarios];
        }

        if (empty($inventarios)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte de Kardex");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Kardex");
        $hojaActiva->getColumnDimension('A')->setWidth(5);
        $hojaActiva->getColumnDimension('B')->setWidth(40);
        $hojaActiva->getColumnDimension('C')->setWidth(20);
        $hojaActiva->getColumnDimension('D')->setWidth(10);
        $hojaActiva->getColumnDimension('E')->setWidth(10);
        $hojaActiva->getColumnDimension('F')->setWidth(20);
        $hojaActiva->getColumnDimension('G')->setWidth(10);

        //texto bold
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
        //centrar cabecera
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
        //color cabecera
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('FF808080');
        //color letras cabecera
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->getColor()->setARGB('FFFFFFFF');
        //bordes
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


        $hojaActiva->setCellValue('A1', 'N°');
        $hojaActiva->setCellValue('B1', 'Producto');
        $hojaActiva->setCellValue('C1', 'Comprobante');
        $hojaActiva->setCellValue('D1', 'Entrada');
        $hojaActiva->setCellValue('E1', 'Salida');
        $hojaActiva->setCellValue('F1', 'Fecha');
        $hojaActiva->setCellValue('G1', 'Saldo');

        $i = 2;
        foreach ($inventarios as $inventario) {
            //borde
            $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':G' . $i)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            //centrar
            $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':G' . $i)->getAlignment()->setHorizontal('center');
            if ($inventario->tipo == "entrada") {
                $inventario->entrada = $inventario->cantidad;
                $inventario->salida = 0;
            }
            if ($inventario->tipo == "salida") {
                $inventario->entrada = 0;
                $inventario->salida = $inventario->cantidad;
            }
            $hojaActiva->setCellValue('A' . $i, $i - 1);
            $hojaActiva->setCellValue('B' . $i, $inventario->codigo . "-" . $inventario->producto);
            $hojaActiva->setCellValue('C' . $i, $inventario->comprobante);
            $hojaActiva->setCellValue('D' . $i, $inventario->entrada);
            $hojaActiva->setCellValue('E' . $i, $inventario->salida);
            $hojaActiva->setCellValue('F' . $i, date('d-m-Y', strtotime($inventario->fecha)));
            $hojaActiva->setCellValue('G' . $i, $inventario->stock_actual);
            $i++;
        }

        $filename = "Kardex-" . $inventarios[0]->codigo . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
