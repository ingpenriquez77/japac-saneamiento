<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */
    'title' => 'JAPAC',
    'title_prefix' => '',
    'title_suffix' => ' | Saneamiento',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    */
    'logo' => '<b>JAPAC</b> Saneamiento',
    'logo_img' => 'dist/img/logo-japac.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'JAPAC Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    | Desactivado para que use el login minimalista que creamos sin que flote
    | el logo por defecto arriba de la tarjeta blanca.
    */
    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'dist/img/logo-japac.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Config
    |--------------------------------------------------------------------------
    */
    'preloader' => [
        'enabled' => true,
        'img' => [
            'path' => 'dist/img/logo-japac.png',
            'alt' => 'JAPAC Preloader Image',
            'class' => 'img-fluid',
            'width' => 100,
            'height' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_topnav' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    */
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    */
    'classes_body' => 'sidebar-mini',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => 'nav-pills nav-sidebar flex-column',
    'classes_sidebar_open' => 'sidebar-open',
    'classes_sidebar_closed' => 'sidebar-collapse',
    'classes_sidebar_mini' => 'sidebar-mini',
    'classes_sidebar_mini_md' => 'sidebar-mini-md',
    'classes_sidebar_mini_lg' => 'sidebar-mini-lg',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Sidebar Images
    |--------------------------------------------------------------------------
    */
    'sidebar_img' => false,
    'sidebar_img_path' => '',

    /*
    |--------------------------------------------------------------------------
    | Menu Items (MÓDULOS DEL SISTEMA JAPAC)
    |--------------------------------------------------------------------------
    */
    'menu' => [
        // Barra de búsqueda en el menú lateral
        [
            'text' => 'search',
            'search' => true,
            'topnav' => false,
        ],
        [
            'text' => 'DASHBOARD PRINCIPAL',
            'url' => 'dashboard',
            'icon' => 'fas =>fw fa-tachometer-alt',
        ],

        // --- SECCIÓN DE SANEAMIENTO ---
        ['header' => 'ÁREA DE SANEAMIENTO'],

        [
            'text' => 'Establecimientos',
            'url' => 'establecimientos',
            'icon' => 'fas =>fw fa-building',
            'label_color' => 'success',
        ],
        [
            'text' => 'Visitas de Inspección',
            'icon' => 'fas =>fw fa-clipboard-list',
            'submenu' => [
                [
                    'text' => 'Inspecciones Formales',
                    'url' => 'inspecciones-formales',
                    'icon' => 'fas =>fw fa-file-contract',
                ],
                [
                    'text' => 'Inspecciones Informales',
                    'url' => 'inspecciones-informales',
                    'icon' => 'fas =>fw fa-file-alt',
                ],
            ],
        ],
        [
            'text' => 'Inicios de Procedimiento',
            'url' => 'procedimientos',
            'icon' => 'fas =>fw fa-gavel',
        ],
        [
            'text' => 'Laboratorios Externos',
            'icon' => 'fas =>fw fa-flask',
            'submenu' => [
                [
                    'text' => 'Resultados Lab',
                    'url' => 'laboratorios',
                    'icon' => 'fas =>fw fa-vial',
                ],
                [
                    'text' => 'IP Lab Externos',
                    'url' => 'ip-laboratorios',
                    'icon' => 'fas =>fw fa-microscope',
                ],
            ],
        ],

        // --- MÓDULO DE CÁLCULO CRÍTICO ---
        [
            'text' => 'Índice Incumplimiento',
            'url' => 'calculo-incumplimiento',
            'icon' => 'fas =>fw fa-calculator',
            'label' => 'Cálculo',
            'label_color' => 'danger',
        ],
        [
            'text' => 'Resolutivo Administrativo',
            'url' => 'resolutivos',
            'icon' => 'fas =>fw fa-file-signature',
        ],

        // --- CONFIGURACIÓN DE USUARIO ---
        ['header' => 'AJUSTES DE CUENTA'],
        [
            'text' => 'Mi Perfil',
            'url' => 'profile',
            'icon' => 'fas =>fw fa-user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    */
    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    */
    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */
    'livewire' => false,
];
