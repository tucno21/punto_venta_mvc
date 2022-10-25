<?php

// DATOS GENERALES ADMIN
$title = 'Punto Venta';
$titleShort = 'PV';
$mainLink = base_url('/dashboard');
$logoAdmin = '../public/logo/logo.png';

//DATOS DEL USUARIO ADMIN
$userName = session()->user()->name;



//MENU CERRAR O PERFIL DE ADMINISTRADOR
$menuSession = [
    [
        'text' => 'Logout',
        'url'  => route('login.logout'),
        'icon' => 'bi bi-box-arrow-right',
    ],
];



//CREACION DE ENLACES PARA EL MENU SIDEBAR
$linksSidebar = [
    can('dashboard.index') ?
        [
            'mode' => 'menu',
            'text' => 'Dashboard',
            'url'  => route('dashboard.index'),
            'icon' => 'bi bi-speedometer2',
        ] : null,
    can('users.index') || can('roles.index') ?
        [
            'mode' => 'submenu',
            'text'    => 'Usuarios',
            'url'    => '#',
            'icon' => 'bi bi-people',
            'submenu' => [
                can('users.index') ?
                    [
                        'text' => 'Usuarios',
                        'url'  => route('users.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('roles.index') ?
                    [
                        'text' => 'Roles',
                        'url'  => route('roles.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                [
                    'text' => 'Permisos',
                    'url'  => route('permissions.index'),
                    'icon' => 'fas fa-circle',
                ],
            ],
        ] : null,
    // can('products.index') ?
    //     [
    //         'mode' => 'menu',
    //         'text' => 'Productos',
    //         'url'  => route('products.index'),
    //         'icon' => 'bi bi-shop',
    //     ] : null,
];



//ENLACES PARA CSS Y JS html
$linkURL = base_url;

$linksCss = [
    $linkURL . '/assets/css/style.css',
    $linkURL . '/assets/css/customizer.css',
    $linkURL . '/assets/css/icon/bootstrap-icons.css',
];

$linksScript = [
    $linkURL . '/assets/js/popper.min.js',
    $linkURL . '/assets/js/perfect-scrollbar.min.js',
    $linkURL . '/assets/js/bootstrap.min.js',
    $linkURL . '/assets/js/feather.min.js',
    $linkURL . '/assets/js/pcoded.js',
];
