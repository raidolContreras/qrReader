<?php

session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $pagina = $_GET['pagina'] ?? 'analytics';
} elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'vigilante') {
    $pagina = $_GET['pagina'] ?? 'qrScan';
} else {
    $pagina = $_GET['pagina'] ?? 'qrScan';
}
$title = '';

$choferNavs = [
    'qrScan' => 'Lector de Qr',
    'routes' => 'Seleccionar una ruta',
];

$vigilanteNavs = [
    'qrScan' => 'Lector de Qr'
];

$adminNavs = [
    'configuration' => 'Configuración',
    'users' => 'Lista de usuarios',
    'pointRegisters' => 'Puntos de registro',
    'analytics' => 'Analíticas',
    'buses' => 'Autobuses',
];

if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    if ($_SESSION['role'] == 'admin') {
        if (array_key_exists($pagina, $adminNavs)) {
            $title = $adminNavs[$pagina];
            includeData($pagina, 'admin');
        } else {
            includeError404();
        }
    } elseif ($_SESSION['role'] == 'vigilante') {
        if (array_key_exists($pagina, $vigilanteNavs)) {
            $title = $vigilanteNavs[$pagina];
            includeDataVigilante($pagina, 'vigilante');
        } else {
            includeError404();
        }
    } else {
        if (array_key_exists($pagina, $choferNavs)) {
            if ($pagina == 'qrScan') {
                if (isset($_SESSION['route'])) {
                    $title = $choferNavs[$pagina];
                    includeDataVigilante($pagina, 'chofer');
                } else {
                    header("Location: routes");
                    exit();
                }
            } else {
                $title = $choferNavs[$pagina];
                includeDataVigilante($pagina, 'chofer');
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

function includeDataVigilante($pagina, $roleNav)
{
    require 'view/css.php';
    require 'view/navs/navbar.php';
    require 'view/navs/sidenavVigilante.php';
    require "view/pages/$roleNav/$pagina.php";
    require 'view/js.php';
}

function includeDataNoNavs($pagina, $roleNav)
{
    require 'view/css.php';
    require "view/pages/$roleNav/$pagina.php";
    require 'view/js.php';
}