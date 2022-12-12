<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Library\FPDF\FPDF;
use App\Model\Proveedores;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProveedorController extends Controller
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
        return view('proveedores/index', [
            'titleGlobal' => 'Proveedores',
        ]);
    }

    public function dataTable()
    {
        $proveedores = Proveedores::get();
        //cuando viene un solo objeto
        if (is_object($proveedores)) {
            $proveedores = [$proveedores];
        }
        //json
        echo json_encode($proveedores);
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
        $data = $_POST;

        $valid = $this->validate($data, [
            'nombre' => 'required',
            'direccion' => 'required',
            'documento' => 'required|unique:Proveedores,documento',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $result = Proveedores::create($data);
            $response = ['status' => true, 'data' => $result->id];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $proveedor = null;
        } else {
            $proveedor = Proveedores::first($id->id);
        }

        $response = ['status' => true, 'data' => $proveedor];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'nombre' => 'required',
            'direccion' => 'required',
            'documento' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Proveedores::update($data->id, $data);
            $response = ['status' => true, 'data' => 'actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = Proveedores::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $pro = Proveedores::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($pro->estado == 1) ? 0 : 1;
        $result = Proveedores::update($data->id, ['estado' => $estado]);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function buscar()
    {
        //busqueda para autocompletar
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Proveedores::getBuscar($data->search);
        if (is_object($response)) {
            $response = [$response];
        }
        foreach ($response as $key => $value) {
            //obligatorio agregar ->textItem
            $response[$key]->textItem = $value->documento . ' - ' . $value->nombre;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function pdf()
    {
        $proveedores = Proveedores::get();
        if (is_object($proveedores)) {
            $proveedores = [$proveedores];
        }
        //$proveedores si es un array vacio
        if (empty($proveedores)) {
            echo "No hay datos para mostrar";
            exit;
        }
        // dd($proveedores);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Reporte de Proveedores');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Lista: Proveedores', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetAutoPageBreak('auto', 2); // 2 es el margen inferior
        $pdf->SetDisplayMode(75); // zoom 75% (opcional)

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(8, 5, utf8_decode('N°'), 1, 0, 'C');
        $pdf->Cell(85, 5, utf8_decode('nombre / Razon Social'), 1, 0, 'C');
        $pdf->Cell(100, 5, utf8_decode('Dirección'), 1, 0, 'C');
        $pdf->Cell(30, 5, utf8_decode('DNI/RUC'), 1, 0, 'C');
        $pdf->Cell(30, 5, utf8_decode('Telefono'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('Estado'), 1, 0, 'C');
        $pdf->Ln(5);

        $i = 1;
        foreach ($proveedores as $proveedore) {
            if ($proveedore->estado == 1)
                $proveedore->condicion = '';
            if ($proveedore->estado == 0)
                $proveedore->condicion = 'Inactivo';

            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(8, 5, $i, 1, 0, 'C');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(85, 5, $proveedore->nombre, 1, 0, 'L');
            $pdf->Cell(100, 5, $proveedore->direccion, 1, 0, 'L');
            $pdf->Cell(30, 5, $proveedore->documento, 1, 0, 'R');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(30, 5, $proveedore->telefono, 1, 0, 'L');
            $pdf->Cell(20, 5, $proveedore->condicion, 1, 0, 'C');
            $pdf->Ln(5);
            $i++;
        }

        $pdf->Output("Reporte-ventas" . ".pdf", "I");
    }

    public function excel()
    {
        $proveedores = Proveedores::get();
        if (is_object($proveedores)) {
            $proveedores = [$proveedores];
        }
        //$proveedores si es un array vacio
        if (empty($proveedores)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte de Proveedores");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Proveedores");
        $hojaActiva->getColumnDimension('A')->setWidth(5); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(40); //nombre
        $hojaActiva->getColumnDimension('C')->setWidth(40); //direccion
        $hojaActiva->getColumnDimension('D')->setWidth(15); //dni
        $hojaActiva->getColumnDimension('E')->setWidth(20); //telefono
        $hojaActiva->getColumnDimension('F')->setWidth(15); //estado

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:F1');
        $hojaActiva->mergeCells('A2:F2');
        //centrar titulo
        $hojaActiva->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
        //font
        $hojaActiva->getStyle('A1:F1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:F1')->getFont()->setSize(16);

        //titulo
        $hojaActiva->setCellValue('A1', 'Reporte: proveedores');
        $hojaActiva->setCellValue('A2', 'Fecha de emision: ' . date('Y-m-d H:i:s'));

        //cabecera
        $hojaActiva->setCellValue('A4', 'N°');
        $hojaActiva->setCellValue('B4', 'Nombre');
        $hojaActiva->setCellValue('C4', 'Direccion');
        $hojaActiva->setCellValue('D4', 'DNI/RUC');
        $hojaActiva->setCellValue('E4', 'Telefono');
        $hojaActiva->setCellValue('F4', 'Estado');
        //centrar
        $hojaActiva->getStyle('A4:F4')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A4:F4')->getFont()->setBold(true);
        $hojaActiva->getStyle('A4:F4')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $i = 1;
        foreach ($proveedores as $proveedore) {
            if ($proveedore->estado == 1)
                $proveedore->condicion = '';
            if ($proveedore->estado == 0)
                $proveedore->condicion = 'Inactivo';

            $hojaActiva->setCellValue('A' . ($i + 4), $i);
            $hojaActiva->setCellValue('B' . ($i + 4), $proveedore->nombre);
            $hojaActiva->setCellValue('C' . ($i + 4), $proveedore->direccion);
            $hojaActiva->setCellValue('D' . ($i + 4), $proveedore->documento);
            $hojaActiva->setCellValue('E' . ($i + 4), $proveedore->telefono);
            $hojaActiva->setCellValue('F' . ($i + 4), $proveedore->condicion);
            //borde
            $hojaActiva->getStyle('A' . ($i + 4) . ':F' . ($i + 4))->getBorders()->getAllBorders()->setBorderStyle('thin');
            $i++;
        }

        $filename = 'proveedores-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
