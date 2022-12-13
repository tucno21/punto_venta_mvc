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
}
