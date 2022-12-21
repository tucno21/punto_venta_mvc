<?php

namespace App\Controller\BackView;

use App\Model\Cajas;
use App\Model\Ventas;
use System\Controller;
use App\Model\NotaVentas;
use App\Library\FPDF\FPDF;
use App\Model\CajasArqueo;
use App\Model\CuentasCobrar;

class CajaArqueoController extends Controller
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
        return view('cajaarqueo/index', [
            'titleGlobal' => 'Cajas Registradoras',
        ]);
    }

    public function dataTable()
    {
        //traer de usuario id
        $usuarioCaja = session()->user()->id;

        $cajasAqueos = CajasArqueo::where('usuario_id', $usuarioCaja)->orderBy('id', 'desc')->get();
        //cuando viene un solo objeto
        if (is_object($cajasAqueos)) {
            $cajasAqueos = [$cajasAqueos];
        }
        //json
        echo json_encode($cajasAqueos);
        exit;
    }

    public function cajas()
    {
        $cajas = Cajas::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($cajas)) {
            $cajas = [$cajas];
        }
        //json
        echo json_encode($cajas);
        exit;
    }

    public function estadocajaarqueo()
    {
        $usuarioCaja = session()->user()->id;

        $estadoCaja = CajasArqueo::ultimaCajaUsuario($usuarioCaja);

        echo json_encode($estadoCaja);
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
            'caja_id' => 'required',
            'monto_inicial' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            //agregar fecha a $data
            $data->fecha_apertura = date('Y-m-d H:i:s');
            //agregar usuario_id a $data
            $data->usuario_id = session()->user()->id;

            CajasArqueo::create($data);
            $response = ['status' => true, 'data' => 'creado correctamente'];
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
        $usuarioCaja = session()->user()->id;
        //traer cajaArqueo
        $cajaArqueo = CajasArqueo::first($data->id);
        //fecha de cierre
        $dataCerrar = new \stdClass();
        $dataCerrar->fecha_cierre = date('Y-m-d H:i:s');
        $dataCerrar->estado = 0;

        $ventas = Ventas::TotalVentas($cajaArqueo->fecha_apertura, $dataCerrar->fecha_cierre, $usuarioCaja);
        $notaventas = NotaVentas::TotalVentas($cajaArqueo->fecha_apertura, $dataCerrar->fecha_cierre, $usuarioCaja);
        $abonos = CuentasCobrar::TotalAbonos($cajaArqueo->fecha_apertura, $dataCerrar->fecha_cierre, $usuarioCaja);


        $dataCerrar->total_venta = $ventas->total + $notaventas->total + $abonos->total;
        $dataCerrar->monto_final = $dataCerrar->total_venta + $cajaArqueo->monto_inicial;

        CajasArqueo::update($data->id, $dataCerrar);
        $response = ['status' => true, 'message' => 'caja arqueo cerrada correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function reporte()
    {
        $data = $this->request()->getInput();
        $usuarioCaja = session()->user()->id;
        $cajaArqueo = CajasArqueo::first($data->id);

        $ventas = Ventas::ventasGeneradas($cajaArqueo->fecha_apertura, $cajaArqueo->fecha_cierre, $usuarioCaja);
        if (is_object($ventas)) {
            $ventas = [$ventas];
        }
        $notaventas = NotaVentas::ventasGeneradas($cajaArqueo->fecha_apertura, $cajaArqueo->fecha_cierre, $usuarioCaja);
        if (is_object($notaventas)) {
            $notaventas = [$notaventas];
        }
        $abonos = CuentasCobrar::abonosGenerados($cajaArqueo->fecha_apertura, $cajaArqueo->fecha_cierre, $usuarioCaja);
        if (is_object($abonos)) {
            $abonos = [$abonos];
        }

        // dd($ventas);
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Detalle de caja arqueo');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Detalle de Caja Arqueo', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 5, 'Fecha Apertura: ' . $cajaArqueo->fecha_apertura, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Fecha Cierre: ' . $cajaArqueo->fecha_cierre, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Monto Inicial: ' . $cajaArqueo->monto_inicial, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Ingresos: ' . $cajaArqueo->total_venta, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Monto Final: ' . $cajaArqueo->monto_final, 0, 1, 'L');
        $pdf->Ln(5);

        // dd($ventas == null);

        if (!empty($ventas)) {
            //========= CONTADO SUNAT
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Ingresos Detalle de Ventas (Sunat)', 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 5, 'Fecha', 1, 0, 'C');
            $pdf->Cell(25, 5, 'comprobante', 1, 0, 'C');
            $pdf->Cell(75, 5, 'Cliente', 1, 0, 'C');
            $pdf->Cell(30, 5, 'Forma de pago', 1, 0, 'C');
            $pdf->Cell(20, 5, 'sub-Total', 1, 0, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 10);

            $totalVentas = 0;
            foreach ($ventas as $venta) {
                if ($venta->forma_pago == 'Contado') {
                    $totalVentas += $venta->total;
                    $pdf->Cell(40, 5, $venta->fecha_emision, 1, 0, 'C');
                    $pdf->Cell(25, 5, $venta->serie . "-" . $venta->correlativo, 1, 0, 'C');
                    $pdf->Cell(75, 5, $venta->cliente, 1, 0, 'L');
                    $pdf->Cell(30, 5, $venta->forma_pago, 1, 0, 'C');
                    $pdf->Cell(20, 5, $venta->total, 1, 1, 'C');
                    //aggregar suna total
                }
            }
            $pdf->Cell(170, 5, 'Total', 1, 0, 'R');
            $pdf->Cell(20, 5, $totalVentas, 1, 1, 'C');
            $pdf->Ln(5);

            //========= CREDITO SUNAT
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Detalle de Ventas Credito (Sunat)', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(0, 5, 'No forman parte del del total de caja', 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 5, 'Fecha', 1, 0, 'C');
            $pdf->Cell(25, 5, 'comprobante', 1, 0, 'C');
            $pdf->Cell(75, 5, 'Cliente', 1, 0, 'C');
            $pdf->Cell(30, 5, 'Forma de pago', 1, 0, 'C');
            $pdf->Cell(20, 5, 'sub-Total', 1, 0, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 10);
            $totalVentasCredito = 0;
            foreach ($ventas as $venta) {
                if ($venta->forma_pago == 'Credito') {
                    $totalVentasCredito += $venta->total;
                    $pdf->Cell(40, 5, $venta->fecha_emision, 1, 0, 'C');
                    $pdf->Cell(25, 5, $venta->serie . "-" . $venta->correlativo, 1, 0, 'C');
                    $pdf->Cell(75, 5, $venta->cliente, 1, 0, 'L');
                    $pdf->Cell(30, 5, $venta->forma_pago, 1, 0, 'C');
                    $pdf->Cell(20, 5, $venta->total, 1, 1, 'C');
                    //aggregar suna total
                }
            }
            $pdf->Cell(170, 5, 'Total', 1, 0, 'R');
            $pdf->Cell(20, 5, $totalVentasCredito, 1, 1, 'C');
            $pdf->Ln(5);
        }
        if (!empty($notaventas)) {
            //========= NOTA DE VENTA
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Ingreso Detalle de Nota de Ventas', 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 5, 'Fecha', 1, 0, 'C');
            $pdf->Cell(25, 5, 'comprobante', 1, 0, 'C');
            $pdf->Cell(75, 5, 'Cliente', 1, 0, 'C');
            $pdf->Cell(30, 5, 'Forma de pago', 1, 0, 'C');
            $pdf->Cell(20, 5, 'sub-Total', 1, 0, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 10);

            $totalNotaVentas = 0;
            foreach ($notaventas as $notaventa) {
                $totalNotaVentas += $notaventa->total;
                $pdf->Cell(40, 5, $notaventa->fecha_emision, 1, 0, 'C');
                $pdf->Cell(25, 5, $notaventa->serie . "-" . $notaventa->correlativo, 1, 0, 'C');
                $pdf->Cell(75, 5, $notaventa->cliente, 1, 0, 'L');
                $pdf->Cell(30, 5, $notaventa->forma_pago, 1, 0, 'C');
                $pdf->Cell(20, 5, $notaventa->total, 1, 0, 'C');
                $pdf->Ln(5);
            }
            $pdf->Cell(170, 5, 'Total', 1, 0, 'R');
            $pdf->Cell(20, 5, $totalNotaVentas, 1, 0, 'C');
            $pdf->Ln(5);
            $pdf->Ln(5);
        }
        if (!empty($abonos)) {
            //========= ABONOS
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Ingreso de Detalle Abonos', 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(60, 5, 'Fecha', 1, 0, 'C');
            $pdf->Cell(60, 5, 'comprobante', 1, 0, 'C');
            $pdf->Cell(0, 5, 'sub-Total', 1, 1, 'C');

            $pdf->SetFont('Arial', '', 10);
            $totalAbonos = 0;
            foreach ($abonos as $abono) {
                $totalAbonos += $abono->monto;
                $pdf->Cell(60, 5, $abono->fecha, 1, 0, 'C');
                $pdf->Cell(60, 5, $abono->serie . "-" . $abono->correlativo, 1, 0, 'C');
                $pdf->Cell(0, 5, $abono->monto, 1, 1, 'C');
            }
            $pdf->Cell(120, 5, 'Total', 1, 0, 'R');
            $pdf->Cell(0, 5, $totalAbonos, 1, 1, 'C');
            $pdf->Ln(5);
        }

        $pdf->Output('DetalleCajaArqueo.pdf', "I");
    }
}
