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
    [
        'mode' => 'submenu',
        'text'    => 'Productos',
        'url'    => '#',
        'icon' => 'bi bi-card-checklist',
        'submenu' => [

            [
                'text' => 'Productos',
                'url'  =>  route('productos.index'),
                'icon' => 'fas fa-circle',
            ],

            [
                'text' => 'Categorias',
                'url'  => route('categorias.index'),
                'icon' => 'fas fa-circle',
            ],
            [
                'text' => 'Unidades',
                'url'  => route('unidades.index'),
                'icon' => 'fas fa-circle',
            ],
        ],
    ],
];



//ENLACES PARA CSS Y JS html
$linkURL = base_url;

$linksCss = [
    $linkURL . '/assets/css/style.css',
    $linkURL . '/assets/css/customizer.css',
    $linkURL . '/assets/css/icon/bootstrap-icons.css',
    $linkURL . '/assets/plugins/simple-datatables/style.css',
];

$linksScript = [
    $linkURL . '/assets/js/popper.min.js',
    $linkURL . '/assets/js/perfect-scrollbar.min.js',
    $linkURL . '/assets/js/bootstrap.min.js',
    $linkURL . '/assets/js/feather.min.js',
    $linkURL . '/assets/js/pcoded.js',
    $linkURL . '/assets/plugins/simple-datatables/simple-datatables.js',
    $linkURL . '/assets/plugins/sweetalert2/sweetalert2.js',
    $linkURL . '/assets/js/funciones.js',
];
