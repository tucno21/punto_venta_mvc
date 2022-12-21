<?php

namespace App\Controller\BackView;

use App\Model\Ventas;
use System\Controller;
use App\Library\FPDF\FPDF;
use App\Model\InfoEmpresa;
use App\Model\CuentasCobrar;

class CuentasCobrarController extends Controller
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
        return view('cuentacobrar/index', [
            'titleGlobal' => 'Cuentas por cobrar',
        ]);
    }

    public function dataTable()
    {
        $creditos = $this->listaCreditos();
        //json
        echo json_encode($creditos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function listaCreditos()
    {
        $creditos = Ventas::getCreditos();
        if (is_object($creditos)) {
            $creditos = [$creditos];
        }
        $abonos = CuentasCobrar::abonoTotalVentaId();
        if (is_object($abonos)) {
            $abonos = [$abonos];
        }

        foreach ($creditos as $key => $credi) {
            $creditos[$key]->abono = "0.00";
            $creditos[$key]->estado_credito = 1;
            foreach ($abonos as $key2 => $abono) {
                if ($credi->id == $abono->venta_id) {
                    $creditos[$key]->abono = $abono->total;
                    if ($credi->total > $abono->total) {
                        $creditos[$key]->estado_credito = 1;
                    } else {
                        $creditos[$key]->estado_credito = 0;
                    }
                }
            }
        }

        return $creditos;
    }

    public function abono()
    {
        $data = $this->request()->getInput();

        $creditos = $this->listaCreditos();

        $creditoid;
        foreach ($creditos as $key => $credi) {
            if ($credi->id == $data->id) {
                $creditoid = $credi;
            }
        }

        echo json_encode($creditoid, JSON_UNESCAPED_UNICODE);
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
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'venta_id' => 'required',
            'monto' => 'required',
            'fecha' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $data->user_id = session()->user()->id;
            CuentasCobrar::create($data);
            $response = ['status' => true, 'message' => 'Abono registrado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function reporte()
    {
        $data = $this->request()->getInput();

        $venta = Ventas::getVentaIdCliente($data->id);
        $abonos = CuentasCobrar::abonosVentaId($data->id);
        if (is_object($abonos)) {
            $abonos = [$abonos];
        }
        $emisor = InfoEmpresa::first();
        // dd($venta);
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Reporte de Cuentas por Cobrar', true);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, utf8_decode($emisor->nombre_comercial), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('Teléfono: ' . $emisor->telefono), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Dirección: ' . $emisor->direccion), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('Correo: ' . $emisor->email), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode('RUC: ' . $emisor->ruc), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, utf8_decode('Reporte de Venta al Credito'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, utf8_decode('Cliente'), 'LT', 0, 'L');
        $pdf->Cell(0, 7, utf8_decode(': ' . $venta->cliente), 'TR', 1, 'L');
        $pdf->Cell(30, 7, utf8_decode('Fecha'), 'LT', 0, 'L');
        $pdf->Cell(0, 7, utf8_decode(': ' . $venta->fecha_emision), 'TR', 1, 'L');
        $pdf->Cell(30, 7, utf8_decode('Comprobante'), 'LT', 0, 'L');
        $pdf->Cell(0, 7, utf8_decode(': ' . $venta->serie . '-' . $venta->correlativo), 'TR', 1, 'L');
        $pdf->Cell(30, 7, utf8_decode('Total'), 'LTB', 0, 'L');
        $pdf->Cell(0, 7, utf8_decode(': ' . $venta->total), 'TRB', 1, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, utf8_decode('Monto y Fecha de Pago Previsto'), 0, 1, 'C');
        $pdf->Ln(5);

        $cuotas = json_decode($venta->cuotas);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 5, utf8_decode('Fecha'), 1, 0, 'C');
        $pdf->Cell(0, 5, utf8_decode('Monto'), 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $total = 0;
        foreach ($cuotas as $key => $cuota) {
            $pdf->Cell(80, 5, utf8_decode($cuota->fecha), 1, 0, 'C');
            $pdf->Cell(0, 5, utf8_decode($cuota->monto), 1, 1, 'C');
            $total += $cuota->monto;
        }
        $pdf->Cell(80, 5, utf8_decode('Total'), 1, 0, 'C');
        $pdf->Cell(0, 5, utf8_decode($total), 1, 1, 'C');
        $pdf->Ln(5);


        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, utf8_decode('Abonos realizados'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 5, utf8_decode('Fecha'), 1, 0, 'C');
        $pdf->Cell(0, 5, utf8_decode('Monto'), 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $total = 0;
        foreach ($abonos as $key => $abono) {
            $pdf->Cell(80, 5, utf8_decode($abono->fecha), 1, 0, 'C');
            $pdf->Cell(0, 5, utf8_decode($abono->monto), 1, 1, 'C');
            $total += $abono->monto;
        }
        $pdf->Cell(80, 5, utf8_decode('Total'), 1, 0, 'C');
        $pdf->Cell(0, 5, utf8_decode($total), 1, 1, 'C');
        // $pdf->Ln(5);
        //Restante
        $pdf->Cell(80, 5, utf8_decode('Saldo'), 1, 0, 'C');
        $pdf->Cell(0, 5, utf8_decode($venta->total - $total), 1, 1, 'C');


        $pdf->Output("Reporte-Credito" . ".pdf", "I");
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
}
