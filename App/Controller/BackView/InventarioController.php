<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Library\FPDF\FPDF;
use App\Model\Inventarios;

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
}
