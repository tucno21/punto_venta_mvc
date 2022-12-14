<?php

namespace App\Controller\Auth;

use App\Model\Users;
use System\Controller;
use App\Library\Email\Email;


class AuthController extends Controller
{
    //ver ventana de login
    public function index()
    {
        if (auth()->has()) {
            return redirect()->route('dashboard.index');
        } else {
            return view('auth/index', [
                'title' => 'Login Mini Framework',
            ]);
        }
    }

    public function store()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'email' => 'required|email|not_unique:Users,email',
            'password' => 'required|password_verify:Users,email',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $user = Users::iniciarSesion($data['email']);

            if ($user->status == 0) {
                $response = ['status' => false, 'message' => 'El usuario esta bloqueado o solicito recuperar la contraseña'];
                //json_encode
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }

            auth()->attempt($user);
            $response = ['status' => true, 'message' => 'Bienvenido ' . $user->name];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    //cerrar sesion
    public function logout()
    {
        auth()->logout();
        return redirect()->route('login.index');
    }

    //ventas de donde se recupera la contraseña a traves de un email
    public function passwordrecover()
    {
        return view('auth/passwordrecover', [
            'title' => 'Recuperar Contraseña',
        ]);
    }

    //enviar email con token como mensaje y cambiar el status a 0
    public function passwordrecoverstore()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'email' => 'required|email|not_unique:Users,email',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $user = Users::select('id', 'email', 'name')
                ->where('email', $data['email'])
                ->get();
            //generar token muy largo
            $user->token = md5(uniqid(rand(), true));
            $user->status = 0;
            Users::update($user->id, $user);

            //enviar email
            $email = new Email($user->email, $user->name);
            $email->token($user->token);
            $result = $email->send();
            if (!$result) {
                $response = ['status' => false, 'message' => 'Error al enviar el email'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }
            $response = ['status' => true, 'message' => 'Enviado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    //mensaje de confirmacion de envio de email
    public function message()
    {
        return view('auth/message', [
            'title' => 'Mensaje de Envio',
        ]);
    }

    //ver si existe el token y en la ventana de reset
    public function reset()
    {
        $data = $this->request()->getInput();

        //preguntar si existe variable token
        if (!isset($data->token)) {
            return view('auth/messagetoken', [
                'title' => 'Error',
            ]);
        }

        $token = Users::select('id', 'token')
            ->where('token', $data->token)
            ->get();

        //si el array esta vacio
        if (empty($token)) {
            return view('auth/messagetoken', [
                'title' => 'Error',
            ]);
        }

        return view('auth/reset', [
            'title' => 'Nuevo Password',
        ]);
    }

    //recibe el token y la nueva contraseña
    public function resetstore()
    {
        $data = $_POST;

        $valid = $this->validate($data, [
            'password' => 'required|min:5',
            // 'password_confirm' => 'required|matches:password',
        ]);

        if ($valid !== true) {
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $user = Users::select('id', 'email')->where('token', $data['token'])->get();
            // $user = Users::where('token', $data['token'])->first();

            //la contraseña ya viene $data y el modelo se encarga de codificar
            $user->token = "";
            $user->status = 1;
            Users::update($user->id, $user);

            // Unidades::create($data);
            $response = ['status' => true, 'message' => 'Actualizado correctamente, ya puedes iniciar sesión'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
