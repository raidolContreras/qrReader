<?php

session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $pagina = $_GET['pagina'] ?? 'analytics';
} elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'coordinador') {
    $pagina = $_GET['pagina'] ?? 'analytics';
} else {
    $pagina = $_GET['pagina'] ?? 'qrScan';
}
$title = '';

$choferNavs = [
    'qrScan' => 'Lector de Qr',
    'routes' => 'Seleccionar una ruta',
];

$coordinatorNavs = [
    'qrScan' => 'Lector de Qr',
    'analytics' => 'Analíticas',
];

$adminNavs = [
    'configuration' => 'Configuración',
    'users' => 'Lista de usuarios',
    'pointRegisters' => 'Puntos de registro',
    'analytics' => 'Analíticas',
];

if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    if ($_SESSION['role'] == 'admin') {
        if (array_key_exists($pagina, $adminNavs)) {
            $title = $adminNavs[$pagina];
            includeData($pagina, 'admin');
        } else {
            includeError404();
        }
    } elseif ($_SESSION['role'] == 'coordinador') {
        if (array_key_exists($pagina, $coordinatorNavs)) {
            $title = $coordinatorNavs[$pagina];
            includeDataCoordinador($pagina, 'coordinador');
        } else {
            includeError404();
        }
    } else {
        if (array_key_exists($pagina, $choferNavs)) {
            if ($pagina == 'qrScan') {
                if (isset($_SESSION['route'])) {
                    $title = $choferNavs[$pagina];
                    includeDataNoNavs($pagina, 'chofer');
                } else {
                    header("Location: routes");
                    exit();
                }
            } else {
                $title = $choferNavs[$pagina];
                includeDataNoNavs($pagina, 'chofer');
            }
        } else {
            $title = 'Error 404';
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
}

function includeDataCoordinador($pagina, $roleNav)
{
    require 'view/css.php';
    require 'view/navs/navbar.php';
    require 'view/navs/sidenavCoordinador.php';
    require "view/pages/$roleNav/$pagina.php";
    require 'view/js.php';
}

function includeDataNoNavs($pagina, $roleNav)
{
    require 'view/css.php';
    require "view/pages/$roleNav/$pagina.php";
    require 'view/js.php';
}