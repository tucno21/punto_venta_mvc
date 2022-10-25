<?php

namespace App\Controller\BackView;

use System\Controller;


class DashboardController extends Controller
{
    public function __construct()
    {
        // enviar los datos de la sesion, y los parametros de la url para validar
        // $this->except(['users', 'users.create'])->middleware('loco');
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard/index', [
            'titulo' => 'Dashboard roles',
        ]);
    }
}
