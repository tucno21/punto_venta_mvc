<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Permissions;

class PermissionController extends Controller
{
    public function __construct()
    {
        //ejecutar para proteger la rutas cuando inicia sesion
        //enviar la sesion y el parametro principal de la url
        $this->middleware('auth');
    }

    public function index()
    {
        $permissions = Permissions::get();

        //cuando viene un solo objeto
        if (is_object($permissions)) {
            $permissions = [$permissions];
        }

        return view('permission.index', [
            'titulo' => 'panel de permisos',
            'permissions' => $permissions,
        ]);
    }

    public function create()
    {
        return view('permission.create', [
            'titulo' => 'crear permisos',
        ]);
    }

    public function store()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'per_name' => 'required',
            'description' => 'required',
        ]);

        if ($valid !== true) {
            return back()->route('permissions.create', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {

            session()->remove('renderView');
            session()->remove('reserveRoute');

            Permissions::create($data);

            return redirect()->route('permissions.index');
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $per = null;
        } else {
            $per = Permissions::first($id->id);
        }

        return view('permission.edit', [
            'titulo' => 'editar permisos',
            'data' => $per,
        ]);
    }

    public function update()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'per_name' => 'required',
            'description' => 'required',
        ]);

        if ($valid !== true) {
            return back()->route('permissions.edit', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {

            session()->remove('renderView');
            session()->remove('reserveRoute');

            Permissions::update($data->id, $data);

            return redirect()->route('permissions.index');
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();

        Permissions::delete((int)$data->id);

        return redirect()->route('permissions.index');
    }
}
