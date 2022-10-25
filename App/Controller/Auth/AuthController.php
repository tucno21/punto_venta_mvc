<?php

namespace App\Controller\Auth;

use App\Model\Auth;
use System\Controller;


class AuthController extends Controller
{
    public function index()
    {
        return view('auth/index', [
            'title' => 'Login Mini Framework',
        ]);
    }

    public function store()
    {
        $data = $this->request()->getInput();

        $valid = $this->validate($data, [
            'email' => 'required|email|not_unique:Auth,email',
            'password' => 'required|password_verify:Auth,email',
        ]);

        if ($valid !== true) {
            return back()->route('login.index', [
                'err' =>  $valid,
                'data' => $data,
            ]);
        } else {
            $user = Auth::select('users.id', 'users.email', 'users.name', 'users.status', 'users.rol_id', 'roles.rol_name')
                ->join('roles', 'users.rol_id', '=', 'roles.id')
                ->get();

            if ($user->status == 0) {
                session()->flash('error', 'El usario ya no esta activo');
                return back()->route('login');
            }

            auth()->attempt($user);

            return redirect()->route('dashboard.index');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login.index');
    }
}
