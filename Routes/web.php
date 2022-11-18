<?php

use System\Route;
use App\Controller\Auth\AuthController;
use App\Controller\BackView\RolController;
use App\Controller\BackView\UserController;
use App\Controller\Factura\MonedaController;
use App\Controller\BackView\UnidadController;
use App\Controller\BackView\ProductoController;
use App\Controller\BackView\CategoriaController;
use App\Controller\BackView\DashboardController;
use App\Controller\BackView\PermissionController;
use App\Controller\Factura\TipoDocumentoController;
use App\Controller\Factura\TipoAfectacionController;
use App\Controller\Factura\TipoComprobanteController;
use App\Controller\BackView\RolesPermissionController;
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
