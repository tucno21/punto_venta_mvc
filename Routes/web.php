<?php

use System\Route;
use App\Controller\Auth\AuthController;
use App\Controller\BackView\RolController;
use App\Controller\BackView\CajaController;
use App\Controller\BackView\UserController;
use App\Controller\BackView\VentaController;
use App\Controller\Factura\MonedaController;
use App\Controller\BackView\CompraController;
use App\Controller\BackView\NotaCDController;
use App\Controller\BackView\UnidadController;
use App\Controller\BackView\ClienteController;
use App\Controller\BackView\ProductoController;
use App\Controller\BackView\CategoriaController;
use App\Controller\BackView\DashboardController;
use App\Controller\BackView\NotaVentaController;
use App\Controller\BackView\ProveedorController;
use App\Controller\BackView\CajaArqueoController;
use App\Controller\BackView\InventarioController;
use App\Controller\BackView\PermissionController;
use App\Controller\BackView\InfoEmpresaController;
use App\Controller\BackView\BuscarDniRucController;
use App\Controller\BackView\FirmaDigitalController;
use App\Controller\Factura\TipoDocumentoController;
use App\Controller\Factura\TipoAfectacionController;
use App\Controller\Factura\TipoComprobanteController;
use App\Controller\BackView\RolesPermissionController;
use App\Controller\Factura\SerieCorrelativoController;
use App\Controller\Factura\TablaParametricaController;

/**
 * cargar el autoloader de composer Y la configuracion de la aplicacion
 */
require_once dirname(__DIR__) . '/System/Autoload.php';

// autenticacion
Route::get('/', [AuthController::class, 'index'])->name('login.index');
Route::post('/', [AuthController::class, 'store']);
Route::get('/logout', [AuthController::class, 'logout'])->name('login.logout');


// BackView
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

//usuarios
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/dataTable', [UserController::class, 'dataTable'])->name('users.dataTable');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/create', [UserController::class, 'store']);
Route::get('/users/edit', [UserController::class, 'edit'])->name('users.edit');
Route::post('/users/edit', [UserController::class, 'update']);
Route::get('/users/status', [UserController::class, 'status'])->name('users.status');
Route::get('/users/destroy', [UserController::class, 'destroy'])->name('users.destroy');

//roles
Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
Route::get('/roles/dataTable', [RolController::class, 'dataTable'])->name('roles.dataTable');
Route::get('/roles/create', [RolController::class, 'create'])->name('roles.create');
Route::post('/roles/create', [RolController::class, 'store']);
Route::get('/roles/edit', [RolController::class, 'edit'])->name('roles.edit');
Route::post('/roles/edit', [RolController::class, 'update']);
Route::get('/roles/destroy', [RolController::class, 'destroy'])->name('roles.destroy');

//permisos
Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
Route::post('/permissions/create', [PermissionController::class, 'store']);
Route::get('/permissions/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
Route::post('/permissions/edit', [PermissionController::class, 'update']);
Route::get('/permissions/destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');
Route::get('/permissions/listaPermissions', [PermissionController::class, 'listaPermissions'])->name('permissions.listaPermissions');

//role y permisos
Route::get('/roles/permissions', [RolesPermissionController::class, 'edit'])->name('roles.permissions');
Route::post('/roles/permissions', [RolesPermissionController::class, 'update']);


//unidad
Route::get('/unidades', [UnidadController::class, 'index'])->name('unidades.index');
Route::get('/unidades/dataTable', [UnidadController::class, 'dataTable'])->name('unidades.dataTable');
Route::get('/unidades/create', [UnidadController::class, 'create'])->name('unidades.create');
Route::post('/unidades/create', [UnidadController::class, 'store']);
Route::get('/unidades/edit', [UnidadController::class, 'edit'])->name('unidades.edit');
Route::post('/unidades/edit', [UnidadController::class, 'update']);
Route::get('/unidades/status', [UnidadController::class, 'status'])->name('unidades.status');
Route::get('/unidades/destroy', [UnidadController::class, 'destroy'])->name('unidades.destroy');

//categorias
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('/categorias/dataTable', [CategoriaController::class, 'dataTable'])->name('categorias.dataTable');
Route::get('/categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('/categorias/create', [CategoriaController::class, 'store']);
Route::get('/categorias/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::post('/categorias/edit', [CategoriaController::class, 'update']);
Route::get('/categorias/status', [CategoriaController::class, 'status'])->name('categorias.status');
Route::get('/categorias/destroy', [CategoriaController::class, 'destroy'])->name('categorias.destroy');

//categorias
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/dataTable', [ProductoController::class, 'dataTable'])->name('productos.dataTable');
Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos/create', [ProductoController::class, 'store']);
Route::get('/productos/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::post('/productos/edit', [ProductoController::class, 'update']);
Route::get('/productos/status', [ProductoController::class, 'status'])->name('productos.status');
Route::get('/productos/destroy', [ProductoController::class, 'destroy'])->name('productos.destroy');
Route::get('/productos/unidades', [ProductoController::class, 'unidades'])->name('productos.unidades');
Route::get('/productos/categorias', [ProductoController::class, 'categorias'])->name('productos.categorias');
Route::get('/productos/afectacion', [ProductoController::class, 'afectacion'])->name('productos.afectacion');
Route::get('/productos/verData', [ProductoController::class, 'verData'])->name('productos.verData');
Route::get('/productos/barcode', [ProductoController::class, 'barcode'])->name('productos.barcode');
Route::get('/productos/inputSearch', [ProductoController::class, 'inputSearch'])->name('productos.inputSearch');
Route::get('/productos/barcodekardex', [ProductoController::class, 'barcodekardex'])->name('productos.barcodekardex');
Route::get('/productos/inputSearchkardex', [ProductoController::class, 'inputSearchkardex'])->name('productos.inputSearchkardex');

//TipoDocumentoController
Route::get('/tipoDocumentos', [TipoDocumentoController::class, 'index'])->name('tipoDocumentos.index');
Route::get('/tipoDocumentos/dataTable', [TipoDocumentoController::class, 'dataTable'])->name('tipoDocumentos.dataTable');
Route::get('/tipoDocumentos/create', [TipoDocumentoController::class, 'create'])->name('tipoDocumentos.create');
Route::post('/tipoDocumentos/create', [TipoDocumentoController::class, 'store']);
Route::get('/tipoDocumentos/edit', [TipoDocumentoController::class, 'edit'])->name('tipoDocumentos.edit');
Route::post('/tipoDocumentos/edit', [TipoDocumentoController::class, 'update']);
Route::get('/tipoDocumentos/status', [TipoDocumentoController::class, 'status'])->name('tipoDocumentos.status');
Route::get('/tipoDocumentos/destroy', [TipoDocumentoController::class, 'destroy'])->name('tipoDocumentos.destroy');

//TipoAfectacionController
Route::get('/tipoAfectaciones', [TipoAfectacionController::class, 'index'])->name('tipoAfectaciones.index');
Route::get('/tipoAfectaciones/dataTable', [TipoAfectacionController::class, 'dataTable'])->name('tipoAfectaciones.dataTable');
Route::get('/tipoAfectaciones/create', [TipoAfectacionController::class, 'create'])->name('tipoAfectaciones.create');
Route::post('/tipoAfectaciones/create', [TipoAfectacionController::class, 'store']);
Route::get('/tipoAfectaciones/edit', [TipoAfectacionController::class, 'edit'])->name('tipoAfectaciones.edit');
Route::post('/tipoAfectaciones/edit', [TipoAfectacionController::class, 'update']);
Route::get('/tipoAfectaciones/status', [TipoAfectacionController::class, 'status'])->name('tipoAfectaciones.status');
Route::get('/tipoAfectaciones/destroy', [TipoAfectacionController::class, 'destroy'])->name('tipoAfectaciones.destroy');


//TipoComprobanteController
Route::get('/tipoComprobantes', [TipoComprobanteController::class, 'index'])->name('tipoComprobantes.index');
Route::get('/tipoComprobantes/dataTable', [TipoComprobanteController::class, 'dataTable'])->name('tipoComprobantes.dataTable');
Route::get('/tipoComprobantes/create', [TipoComprobanteController::class, 'create'])->name('tipoComprobantes.create');
Route::post('/tipoComprobantes/create', [TipoComprobanteController::class, 'store']);
Route::get('/tipoComprobantes/edit', [TipoComprobanteController::class, 'edit'])->name('tipoComprobantes.edit');
Route::post('/tipoComprobantes/edit', [TipoComprobanteController::class, 'update']);
Route::get('/tipoComprobantes/status', [TipoComprobanteController::class, 'status'])->name('tipoComprobantes.status');
Route::get('/tipoComprobantes/destroy', [TipoComprobanteController::class, 'destroy'])->name('tipoComprobantes.destroy');

//TablaParametricaController
Route::get('/tablaParametricas', [TablaParametricaController::class, 'index'])->name('tablaParametricas.index');
Route::get('/tablaParametricas/dataTable', [TablaParametricaController::class, 'dataTable'])->name('tablaParametricas.dataTable');
Route::get('/tablaParametricas/create', [TablaParametricaController::class, 'create'])->name('tablaParametricas.create');
Route::post('/tablaParametricas/create', [TablaParametricaController::class, 'store']);
Route::get('/tablaParametricas/edit', [TablaParametricaController::class, 'edit'])->name('tablaParametricas.edit');
Route::post('/tablaParametricas/edit', [TablaParametricaController::class, 'update']);
Route::get('/tablaParametricas/status', [TablaParametricaController::class, 'status'])->name('tablaParametricas.status');
Route::get('/tablaParametricas/destroy', [TablaParametricaController::class, 'destroy'])->name('tablaParametricas.destroy');

//MonedaController
Route::get('/monedas', [MonedaController::class, 'index'])->name('monedas.index');
Route::get('/monedas/dataTable', [MonedaController::class, 'dataTable'])->name('monedas.dataTable');
Route::get('/monedas/create', [MonedaController::class, 'create'])->name('monedas.create');
Route::post('/monedas/create', [MonedaController::class, 'store']);
Route::get('/monedas/edit', [MonedaController::class, 'edit'])->name('monedas.edit');
Route::post('/monedas/edit', [MonedaController::class, 'update']);
Route::get('/monedas/status', [MonedaController::class, 'status'])->name('monedas.status');
Route::get('/monedas/destroy', [MonedaController::class, 'destroy'])->name('monedas.destroy');

//SerieCorrelativoController
Route::get('/serieCorrelativos', [SerieCorrelativoController::class, 'index'])->name('serieCorrelativos.index');
Route::get('/serieCorrelativos/dataTable', [SerieCorrelativoController::class, 'dataTable'])->name('serieCorrelativos.dataTable');
Route::get('/serieCorrelativos/create', [SerieCorrelativoController::class, 'create'])->name('serieCorrelativos.create');
Route::post('/serieCorrelativos/create', [SerieCorrelativoController::class, 'store']);
Route::get('/serieCorrelativos/edit', [SerieCorrelativoController::class, 'edit'])->name('serieCorrelativos.edit');
Route::post('/serieCorrelativos/edit', [SerieCorrelativoController::class, 'update']);
Route::get('/serieCorrelativos/status', [SerieCorrelativoController::class, 'status'])->name('serieCorrelativos.status');
Route::get('/serieCorrelativos/destroy', [SerieCorrelativoController::class, 'destroy'])->name('serieCorrelativos.destroy');

//InfoEmpresaController
Route::get('/infoEmpresas', [InfoEmpresaController::class, 'index'])->name('infoEmpresas.index');
Route::get('/infoEmpresas/edit', [InfoEmpresaController::class, 'edit'])->name('infoEmpresas.edit');
Route::post('/infoEmpresas/edit', [InfoEmpresaController::class, 'update']);

//FirmaDigitalController
Route::get('/firmaDigitals/edit', [FirmaDigitalController::class, 'edit'])->name('firmaDigitals.edit');
Route::post('/firmaDigitals/edit', [FirmaDigitalController::class, 'update']);
Route::get('/firmaDigitals/ver', [FirmaDigitalController::class, 'ver'])->name('firmaDigitals.ver');


//ClienteController
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/dataTable', [ClienteController::class, 'dataTable'])->name('clientes.dataTable');
Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
Route::post('/clientes/create', [ClienteController::class, 'store']);
Route::get('/clientes/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::post('/clientes/edit', [ClienteController::class, 'update']);
Route::get('/clientes/status', [ClienteController::class, 'status'])->name('clientes.status');
Route::get('/clientes/destroy', [ClienteController::class, 'destroy'])->name('clientes.destroy');
Route::get('/clientes/tipodocumento', [ClienteController::class, 'tipodocumento'])->name('clientes.tipodocumento');
Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');

//ProveedorController
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::get('/proveedores/dataTable', [ProveedorController::class, 'dataTable'])->name('proveedores.dataTable');
Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
Route::post('/proveedores/create', [ProveedorController::class, 'store']);
Route::get('/proveedores/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
Route::post('/proveedores/edit', [ProveedorController::class, 'update']);
Route::get('/proveedores/status', [ProveedorController::class, 'status'])->name('proveedores.status');
Route::get('/proveedores/destroy', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
Route::get('/proveedores/buscar', [ProveedorController::class, 'buscar'])->name('proveedores.buscar');
Route::get('/proveedores/pdf', [ProveedorController::class, 'pdf'])->name('proveedores.pdf');
Route::get('/proveedores/excel', [ProveedorController::class, 'excel'])->name('proveedores.excel');

//CompraController
Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
Route::get('/compras/dataTable', [CompraController::class, 'dataTable'])->name('compras.dataTable');
Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
Route::post('/compras/create', [CompraController::class, 'store']);
Route::get('/compras/destroy', [CompraController::class, 'destroy'])->name('compras.destroy');
Route::get('/compras/tipocomprobante', [CompraController::class, 'tipocomprobante'])->name('compras.tipocomprobante');
Route::get('/compras/reporte', [CompraController::class, 'reporte'])->name('compras.reporte');
Route::get('/compras/compraspdf', [CompraController::class, 'compraspdf'])->name('compras.compraspdf');
Route::get('/compras/comprasexcel', [CompraController::class, 'comprasexcel'])->name('compras.comprasexcel');

//VentaController
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/dataTable', [VentaController::class, 'dataTable'])->name('ventas.dataTable');
Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create');
Route::post('/ventas/create', [VentaController::class, 'store']);
Route::get('/ventas/destroy', [VentaController::class, 'destroy'])->name('ventas.destroy');
Route::get('/ventas/tipocomprobante', [VentaController::class, 'tipocomprobante'])->name('ventas.tipocomprobante');
Route::get('/ventas/serieCorrelativo', [VentaController::class, 'serieCorrelativo'])->name('ventas.serieCorrelativo');
Route::get('/ventas/monedas', [VentaController::class, 'monedas'])->name('ventas.monedas');
Route::get('/ventas/reporte', [VentaController::class, 'reporte'])->name('ventas.reporte');
// Route::get('/ventas/generarXML', [VentaController::class, 'generarXML'])->name('ventas.generarXML');
Route::get('/ventas/enviarSunat', [VentaController::class, 'enviarSunat'])->name('ventas.enviarSunat');
Route::get('/ventas/downloadxml', [VentaController::class, 'downloadxml'])->name('ventas.downloadxml');
Route::get('/ventas/downloadcdr', [VentaController::class, 'downloadcdr'])->name('ventas.downloadcdr');
Route::get('/ventas/boleta', [VentaController::class, 'boleta'])->name('ventas.boleta');
Route::get('/ventas/factura', [VentaController::class, 'factura'])->name('ventas.factura');
Route::get('/ventas/ventaspdf', [VentaController::class, 'ventaspdf'])->name('ventas.ventaspdf');
Route::get('/ventas/ventasexcel', [VentaController::class, 'ventasexcel'])->name('ventas.ventasexcel');

//NotaCDController
Route::get('/notaCDs', [NotaCDController::class, 'index'])->name('notaCDs.index');
Route::get('/notaCDs/dataTable', [NotaCDController::class, 'dataTable'])->name('notaCDs.dataTable');
Route::get('/notaCDs/create', [NotaCDController::class, 'create'])->name('notaCDs.create');
Route::post('/notaCDs/create', [NotaCDController::class, 'store']);
Route::get('/notaCDs/destroy', [NotaCDController::class, 'destroy'])->name('notaCDs.destroy');
Route::get('/notaCDs/tipocomprobante', [NotaCDController::class, 'tipocomprobante'])->name('notaCDs.tipocomprobante');
Route::get('/notaCDs/serieCorrelativo', [NotaCDController::class, 'serieCorrelativo'])->name('notaCDs.serieCorrelativo');
Route::get('/notaCDs/tiponota', [NotaCDController::class, 'tiponota'])->name('notaCDs.tiponota');
Route::get('/notaCDs/reporte', [NotaCDController::class, 'reporte'])->name('notaCDs.reporte');
// Route::get('/notaCDs/generarXML', [NotaCDController::class, 'generarXML'])->name('notaCDs.generarXML');
Route::get('/notaCDs/enviarSunat', [NotaCDController::class, 'enviarSunat'])->name('notaCDs.enviarSunat');
Route::get('/notaCDs/downloadxml', [NotaCDController::class, 'downloadxml'])->name('notaCDs.downloadxml');
Route::get('/notaCDs/downloadcdr', [NotaCDController::class, 'downloadcdr'])->name('notaCDs.downloadcdr');
Route::get('/notaCDs/venta', [NotaCDController::class, 'venta'])->name('notaCDs.venta');
Route::get('/notaCDs/BuscarVenta', [NotaCDController::class, 'BuscarVenta'])->name('notaCDs.BuscarVenta');


//NotaVentaController
Route::get('/notaventas', [NotaVentaController::class, 'index'])->name('notaventas.index');
Route::get('/notaventas/dataTable', [NotaVentaController::class, 'dataTable'])->name('notaventas.dataTable');
Route::get('/notaventas/create', [NotaVentaController::class, 'create'])->name('notaventas.create');
Route::post('/notaventas/create', [NotaVentaController::class, 'store']);
Route::get('/notaventas/destroy', [NotaVentaController::class, 'destroy'])->name('notaventas.destroy');
Route::get('/notaventas/tipocomprobante', [NotaVentaController::class, 'tipocomprobante'])->name('notaventas.tipocomprobante');
Route::get('/notaventas/serieCorrelativo', [NotaVentaController::class, 'serieCorrelativo'])->name('notaventas.serieCorrelativo');
Route::get('/notaventas/monedas', [NotaVentaController::class, 'monedas'])->name('notaventas.monedas');
Route::get('/notaventas/reporte', [NotaVentaController::class, 'reporte'])->name('notaventas.reporte');
Route::get('/notaventas/updateElectronico', [NotaVentaController::class, 'updateElectronico'])->name('notaventas.updateElectronico');
Route::get('/notaventas/ventaspdf', [NotaVentaController::class, 'ventaspdf'])->name('notaventas.ventaspdf');
Route::get('/notaventas/ventasexcel', [NotaVentaController::class, 'ventasexcel'])->name('notaventas.ventasexcel');


//CajaController
Route::get('/cajas', [CajaController::class, 'index'])->name('cajas.index');
Route::get('/cajas/dataTable', [CajaController::class, 'dataTable'])->name('cajas.dataTable');
Route::get('/cajas/create', [CajaController::class, 'create'])->name('cajas.create');
Route::post('/cajas/create', [CajaController::class, 'store']);
Route::get('/cajas/edit', [CajaController::class, 'edit'])->name('cajas.edit');
Route::post('/cajas/edit', [CajaController::class, 'update']);
Route::get('/cajas/status', [CajaController::class, 'status'])->name('cajas.status');
Route::get('/cajas/destroy', [CajaController::class, 'destroy'])->name('cajas.destroy');

//CajaArqueoController
Route::get('/cajaArqueos', [CajaArqueoController::class, 'index'])->name('cajaArqueos.index');
Route::get('/cajaArqueos/dataTable', [CajaArqueoController::class, 'dataTable'])->name('cajaArqueos.dataTable');
Route::get('/cajaArqueos/create', [CajaArqueoController::class, 'create'])->name('cajaArqueos.create');
Route::post('/cajaArqueos/create', [CajaArqueoController::class, 'store']);
Route::get('/cajaArqueos/destroy', [CajaArqueoController::class, 'destroy'])->name('cajaArqueos.destroy');
Route::get('/cajaArqueos/reporte', [CajaArqueoController::class, 'reporte'])->name('cajaArqueos.reporte');
Route::get('/cajaArqueos/cajas', [CajaArqueoController::class, 'cajas'])->name('cajaArqueos.cajas');
Route::get('/cajaArqueos/estadocaja', [CajaArqueoController::class, 'estadocajaarqueo'])->name('cajaArqueos.estadocaja');

//InventarioController
Route::get('/inventarios', [InventarioController::class, 'index'])->name('inventarios.index');
Route::get('/inventarios/dataTable', [InventarioController::class, 'dataTable'])->name('inventarios.dataTable');
Route::get('/inventarios/searchmonth', [InventarioController::class, 'searchmonth'])->name('inventarios.searchmonth');
Route::get('/inventarios/monthpdf', [InventarioController::class, 'monthpdf'])->name('inventarios.monthpdf');
Route::get('/inventarios/monthexcel', [InventarioController::class, 'monthexcel'])->name('inventarios.monthexcel');
Route::get('/inventarios/kardex', [InventarioController::class, 'kardex'])->name('inventarios.kardex');
Route::get('/inventarios/tablekardex', [InventarioController::class, 'tablekardex'])->name('inventarios.tablekardex');
Route::get('/inventarios/searchdate', [InventarioController::class, 'searchdate'])->name('inventarios.searchdate');
Route::get('/inventarios/kardexpdf', [InventarioController::class, 'kardexpdf'])->name('inventarios.kardexpdf');
Route::get('/inventarios/kardexexcel', [InventarioController::class, 'kardexexcel'])->name('inventarios.kardexexcel');
