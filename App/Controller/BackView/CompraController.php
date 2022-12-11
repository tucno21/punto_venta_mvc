<?php

namespace App\Controller\BackView;

use App\Model\Compras;
use System\Controller;
use App\Model\Productos;
use App\Library\FPDF\FPDF;
use App\Model\Inventarios;
use App\Model\Factura\TipoComprobante;

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
}
