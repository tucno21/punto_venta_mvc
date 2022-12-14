<?php

namespace App\Controller\BackView;

use App\Model\Roles;
use System\Controller;
use App\Model\Permissions;

class RolesPermissionController extends Controller
{
    public function __construct()
    {
        //ejecutar para proteger la rutas cuando inicia sesion
        //enviar la sesion y el parametro principal de la url
        $this->middleware('auth');
    }

    public function edit()
    {
        $data = $this->request()->getInput();

        $permisosRol = Permissions::permisosRol((int)$data->id);

        //cuando viene un solo objeto
        if (is_object($permisosRol)) {
            $permisosRol = [$permisosRol];
        }

        $permissions = Permissions::select('id', 'per_name', 'description')->get();

        $rol = Roles::select('id', 'rol_name')->where('id', (int)$data->id)->get();

        $group = [
            'dashboard' => 'Dashboard',
            'users' => 'Usuarios',
            'roles' => 'Roles',
            'productos' => 'Productos',
            'categorias' => 'Categorias',
            'unidades' => 'Unidades',
            'clientes' => 'Clientes',
            'proveedores' => 'Proveedores',
            'compras' => 'Compras',
            'ventas' => 'Ventas',
            'notaventas' => 'Nota de ventas',
            'notaCDs' => 'Nota de Credito y Debito',
            'cajas' => 'Cajas',
            'cajaArqueos' => 'Caja y Arqueos',
            'inventarios' => 'Inventarios',
            'monedas' => 'Monedas',
            'serieCorrelativos' => 'Serie Correlativos',
            'tipoAfectaciones' => 'Tipo  Afectaciones',
            'tablaParametricas' => 'Tabla Parametricas',
            'tipoComprobantes' => 'Tipo de Comprobantes',
            'tipoDocumentos' => 'Tipo de Documentos',
        ];

        foreach ($group as $nameKey => $titleKey) {
            $permissionsGroup[$nameKey] = [];
            foreach ($permissions as $groupPermission) {
                //strstr devuelve la parte de la cadena antes del primer punto
                if (strstr($groupPermission->per_name, '.', true) == $nameKey) {
                    //agregar $titleKey
                    $groupPermission->title = $titleKey;
                    $permissionsGroup[$nameKey][] = $groupPermission;
                }
            }
        }
        // dd($permissionsGroup);

        return view('roles.permission', [
            'titulo' => 'control de permisos',
            // 'permissions' => $permissions,
            'permisosRol' => $permisosRol,
            'rol' => $rol,
            // 'group' => $group,
            'permissionsGroup' => $permissionsGroup

        ]);
    }

    public function update()
    {
        $data = $this->request()->getInput();

        $permisos = $data;
        $permisos = array_filter((array)$permisos, function ($key) {
            return $key !== 'rol_id';
        }, ARRAY_FILTER_USE_KEY);

        Permissions::sync((int)$data->rol_id, $permisos);

        return redirect()->route('roles.index');
    }
}
