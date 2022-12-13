<?php

namespace App\Controller\BackView;

use App\Model\Roles;
use App\Model\Users;
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
        return view('users.index', [
            'titleGlobal' => 'Usuarios',
        ]);
    }

    public function dataTable()
    {
        $users = Users::select('users.id', 'users.email', 'users.name', 'users.status', 'roles.rol_name')
            ->join('roles', 'users.rol_id', '=', 'roles.id')
            ->get();

        //cuando viene un solo objeto
        if (is_object($users)) {
            $users = [$users];
        }
        // dd($users);
        //json
        echo json_encode($users);
        exit;
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
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'name' => 'required|text',
            'email' => 'required|email|unique:Users,email',
            'password' => 'required|min:3|max:12',
            'rol_id' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Users::create($data);
            $response = ['status' => true, 'data' => 'creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $user = null;
        } else {
            $user = Users::select('id', 'name', 'email', 'status', 'rol_id')
                ->where('id', $id->id)
                ->get();
        }

        $response = ['status' => true, 'data' => $user];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'name' => 'required|text',
            'email' => 'required|email|not_unique:Users,email',
            'password' => 'required|min:3|max:12',
            'rol_id' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Users::update($data->id, $data);
            $response = ['status' => true, 'data' => 'Actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = Users::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $user = Users::select('id', 'status')->where('id', $data->id)->get();
        // dd($user);
        $status = ($user->status == 1) ? 0 : 1;
        $result = Users::update($data->id, ['status' => $status]);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function changePassword()
    {
        return view('users/changePassword', [
            'titleGlobal' => 'Cambiar Contraseña',
        ]);
    }

    public function changePasswordPost()
    {
        $data = (object)$_POST;

        $valid = $this->validate($data, [
            'password' => 'required',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Users::update($data->id, $data);
            $response = ['status' => true, 'message' => 'Actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
