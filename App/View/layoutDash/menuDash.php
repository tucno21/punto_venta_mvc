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
        'text' => 'Cerrar Sesión',
        'url'  => route('login.logout'),
        'icon' => 'bi bi-box-arrow-right',
    ],
    [
        'text' => 'Cambiar Contraseña',
        'url'  => route('users.changePassword'),
        'icon' => 'bi bi-key',
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
    can('productos.index') || can('categorias.index') || can('unidades.index') ?
        [
            'mode' => 'submenu',
            'text'    => 'Productos',
            'url'    => '#',
            'icon' => 'bi bi-card-checklist',
            'submenu' => [
                can('productos.index') ?
                    [
                        'text' => 'Productos',
                        'url'  =>  route('productos.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('categorias.index') ?
                    [
                        'text' => 'Categorias',
                        'url'  => route('categorias.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('unidades.index') ?
                    [
                        'text' => 'Unidades',
                        'url'  => route('unidades.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
            ],
        ] : null,
    can('clientes.index') ?
        [
            'mode' => 'menu',
            'text' => 'Clientes',
            'url'  => route('clientes.index'),
            'icon' => 'bi bi-people',
        ] : null,
    can('proveedores.index') ?
        [
            'mode' => 'menu',
            'text' => 'Proveedores',
            'url'  => route('proveedores.index'),
            'icon' => 'bi bi-truck',
        ] : null,
    can('compras.index') ?
        [
            'mode' => 'menu',
            'text' => 'Compras',
            'url'  => route('compras.index'),
            'icon' => 'bi bi-cart2',
        ] : null,
    can('ventas.index') ?
        [
            'mode' => 'menu',
            'text' => 'Ventas',
            'url'  => route('ventas.index'),
            'icon' => 'bi bi-cash-coin',
        ] : null,
    can('ventas.create') ?
        [
            'mode' => 'menu',
            'text' => 'Nuevo Venta',
            'url'  => route('ventas.create'),
            'icon' => 'bi bi-piggy-bank',
        ] : null,
    can('notaventas.index') ?
        [
            'mode' => 'menu',
            'text' => 'Ventas Internas',
            'url'  => route('notaventas.index'),
            'icon' => 'bi bi-receipt',
        ] : null,
    can('notaventas.create') ?
        [
            'mode' => 'menu',
            'text' => 'Nuevo Venta Interna',
            'url'  => route('notaventas.create'),
            'icon' => 'bi bi-piggy-bank',
        ] : null,
    can('notaCDs.index') ?
        [
            'mode' => 'menu',
            'text' => 'Notas C/D',
            'url'  => route('notaCDs.index'),
            'icon' => 'bi bi-sticky',
        ] : null,
    can('cajas.index') || can('cajaArqueos.index') ?
        [
            'mode' => 'submenu',
            'text'    => 'Cajas',
            'url'    => '#',
            'icon' => 'bi bi-inboxes',
            'submenu' => [
                can('cajas.index') ?
                    [
                        'text' => 'Cajas Registradoras',
                        'url'  =>  route('cajas.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('cajaArqueos.index') ?
                    [
                        'text' => 'Caja Aper-Cierre',
                        'url'  => route('cajaArqueos.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
            ],
        ] : null,
    can('inventarios.index') || can('inventarios.kardex') ?
        [
            'mode' => 'submenu',
            'text'    => 'Inventario & Kardex',
            'url'    => '#',
            'icon' => 'bi bi-inboxes',
            'submenu' => [
                can('inventarios.index') ?
                    [
                        'text' => 'Inventario',
                        'url'  => route('inventarios.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('inventarios.kardex') ?
                    [
                        'text' => 'Kardex',
                        'url'  => route('inventarios.kardex'),
                        'icon' => 'fas fa-circle',
                    ] : null,
            ],
        ] : null,

    can('infoEmpresas.index') || can('configEmails.index') ?
        [
            'mode' => 'submenu',
            'text'    => 'Empresa',
            'url'    => '#',
            'icon' => 'bi bi-buildings',
            'submenu' => [
                can('infoEmpresas.index') ?
                    [
                        'text' => 'Empresa',
                        'url'  => route('infoEmpresas.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('configEmails.index') ?
                    [
                        'text' => 'Config. Email',
                        'url'  => route('configEmails.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
            ],
        ] : null,
    can('monedas.index') || can('serieCorrelativos.index') || can('tipoAfectaciones.index') || can('tablaParametricas.index') || can('tipoComprobantes.index') || can('tipoDocumentos.index') ?
        [
            'mode' => 'submenu',
            'text'    => 'Base Factura',
            'url'    => '#',
            'icon' => 'bi bi-file-binary',
            'submenu' => [
                can('monedas.index')  ?
                    [
                        'text' => 'Monedas',
                        'url'  =>  route('monedas.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('serieCorrelativos.index') ?
                    [
                        'text' => 'Series y Correlativos',
                        'url'  =>  route('serieCorrelativos.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('tipoAfectaciones.index') ?
                    [
                        'text' => 'Tipo Afectación',
                        'url'  =>  route('tipoAfectaciones.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                ('tablaParametricas.index') ?
                    [
                        'text' => 'Tabla Parametrica',
                        'url'  =>  route('tablaParametricas.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('tipoComprobantes.index') ?
                    [
                        'text' => 'Tipo Comprobante',
                        'url'  =>  route('tipoComprobantes.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
                can('tipoDocumentos.index') ?
                    [
                        'text' => 'Tipo Documento',
                        'url'  =>  route('tipoDocumentos.index'),
                        'icon' => 'fas fa-circle',
                    ] : null,
            ],
        ] : null,
];

// dd($linksSidebar);



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
    $linkURL . '/assets/plugins/apexcharts/apexcharts.js',
    $linkURL . '/assets/js/funciones.js',
];
