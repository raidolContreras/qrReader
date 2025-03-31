<?php

session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $pagina = $_GET['pagina'] ?? 'Users';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'moderador') {
    $pagina = $_GET['pagina'] ?? 'Users';
} else {
    $pagina = $_GET['pagina'] ?? 'qrScan';
}

$userNavs = [
    'qrScan' => 'Lector de Qr',
    'routes'=> 'Seleccionar una ruta',
];

$moderadorNavs = [
    'Users' => 'Usuarios'
];

$adminNavs = [
    'configuration' => 'Configuración',
    'Users' => 'Lista de usuarios',
    'routes'=> 'Lista de rutas',
];

if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    if ($_SESSION['role'] == 'admin') {
        if (array_key_exists($pagina, $adminNavs)) {
            $title = $adminNavs[$pagina];
            includeData($pagina, 'admin');
        } else {
            includeError404();
        }
    } elseif ($_SESSION['role'] == 'moderador') {
        if (array_key_exists($pagina, $moderadorNavs)) {
            $title = $moderadorNavs[$pagina];
            includeData($pagina, 'moderador');
        } else {
            includeError404();
        }
    } else {
        if (array_key_exists($pagina, $userNavs)) {
            if ($pagina == 'qrScan') {
                if (isset($_SESSION['route'])) {
                    $title = $userNavs[$pagina];
                    includeDataNoNavs($pagina, 'user');
                } else {
                    header("Location: routes");
                    exit();
                }
            } else {
                $title = $userNavs[$pagina];
                includeDataNoNavs($pagina, 'user');
            }
        } else {
            includeError404();
        }
    }
} elseif ($pagina == 'Login') {
    $title = 'Iniciar sesión';
    include "view/pages/auth/login.php";
} else {
    header("Location: Login");
    exit();
}

function includeError404()
{
    include 'error404.php';
}

function includeData($pagina, $roleNav)
{
    require 'view/css.php';
    require 'view/navs/navbar.php';
    require 'view/navs/sidenav.php';
    require "view/pages/$roleNav/$pagina.php";
    require 'view/js.php';
    echo "
    <footer>
        <p>&copy; 2025 Gestión de Usuarios</p>
    </footer>
    ";
}

function includeDataNoNavs($pagina, $roleNav) {
    require 'view/css.php';
    require "view/pages/$roleNav/$pagina.php";
    require 'view/js.php';
}