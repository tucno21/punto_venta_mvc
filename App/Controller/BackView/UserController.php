<?php

namespace App\Controller\BackView;

use App\Model\Auth;
use App\Model\Roles;
use System\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        //ejecutar para proteger la rutas cuando inicia sesion
        //enviar la sesion y el parametro principal de la url
        $this->middleware('auth');
    }

    public function index()
    {
        $users = Auth::select('users.id', 'users.email', 'users.name', 'users.status', 'roles.rol_name')
            ->join('roles', 'users.rol_id', '=', 'roles.id')
            ->get();

        //cuando viene un solo objeto
        if (is_object($users)) {
            $users = [$users];
        }
        // dd($user);

        return view('users.index', [
            'titulo' => 'lista de usuarios',
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = Roles::get();

        return view('users.create', [
            'titulo' => 'crear usuarios',
            'roles' => $roles,
        ]);
    }

    public function store()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'name' => 'required|text',
            'email' => 'required|email|unique:Auth,email',
            'password' => 'required|min:3|max:12',
            'rol_id' => 'required',
        ]);

        if ($valid !== true) {
            return back()->route('users.create', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {

            session()->remove('renderView');
            session()->remove('reserveRoute');

            Auth::create($data);

            return redirect()->route('users.index');
        }
    }

    public function edit()
    {
        $roles = Roles::get();

        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $user = null;
        } else {
            // $user = Auth::first($id->id);
            $user = Auth::select('id', 'name', 'email', 'status', 'rol_id')
                ->where('id', $id->id)
                ->get();
        }

        return view('users.edit', [
            'titulo' => 'actualizar usuarios',
            'roles' => $roles,
            'data' => $user,
        ]);
    }

    public function update()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'name' => 'required|text',
            'email' => 'required|email|not_unique:Auth,email',
            'password' => 'required|min:3|max:12',
            'rol_id' => 'required',
        ]);

        if ($valid !== true) {
            return back()->route('users.edit', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {

            session()->remove('renderView');
            session()->remove('reserveRoute');

            // Auth::create($data);
            Auth::update($data->id, $data);

            return redirect()->route('users.index');
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        // dd((int)$data->id);
        $result = Auth::delete((int)$data->id);
        // dd($result);
        return redirect()->route('users.index');
    }
}
