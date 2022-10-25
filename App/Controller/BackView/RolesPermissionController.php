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

        $group = ['dashboard' => 'Dashboard', 'users' => 'Usuarios', 'roles' => 'Roles'];

        foreach ($group as $g => $v) {
            $permissionsGroup[$g] = [];
            foreach ($permissions as $p) {
                if (strpos($p->per_name, $g) !== false) {
                    //agregar $v
                    $p->title = $v;
                    $permissionsGroup[$g][] = $p;
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
