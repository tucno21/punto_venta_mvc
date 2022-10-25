<?php

namespace App\Controller\BackView;

use App\Model\Roles;
use System\Controller;

class RolController extends Controller
{
    public function __construct()
    {
        //ejecutar para proteger la rutas cuando inicia sesion
        //enviar la sesion y el parametro principal de la url
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Roles::get();
        // dd($roles);

        //cuando viene un solo objeto
        if (is_object($roles)) {
            $roles = [$roles];
        }

        return view('roles.index', [
            'titulo' => 'panel de roles',
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        return view('roles.create', [
            'titulo' => 'crear roles',
        ]);
    }

    public function store()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'rol_name' => 'required',
        ]);

        if ($valid !== true) {
            return back()->route('roles.create', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {

            session()->remove('renderView');
            session()->remove('reserveRoute');

            Roles::create($data);

            return redirect()->route('roles.index');
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $rol = null;
        } else {
            $rol = Roles::first($id->id);
        }

        return view('roles.edit', [
            'titulo' => 'editar roles',
            'data' => $rol,
        ]);
    }

    public function update()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'rol_name' => 'required',
        ]);

        if ($valid !== true) {
            return back()->route('roles.edit', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {

            session()->remove('renderView');
            session()->remove('reserveRoute');

            Roles::update($data->id, $data);

            return redirect()->route('roles.index');
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();

        $result = Roles::delete((int)$data->id);

        return redirect()->route('roles.index');
    }
}
