<?php

use System\Route;
use App\Controller\Auth\AuthController;
use App\Controller\BackView\RolController;
use App\Controller\BackView\UserController;
use App\Controller\BackView\UnidadController;
use App\Controller\BackView\ProductoController;
use App\Controller\BackView\CategoriaController;
use App\Controller\BackView\DashboardController;
use App\Controller\BackView\PermissionController;
use App\Controller\BackView\RolesPermissionController;

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
