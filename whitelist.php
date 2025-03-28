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
    'qrScan'
];

$moderadorNavs = [
    'Users',
];

$adminNavs = [
    'configuration',
    'Users'
];

if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    if ($_SESSION['role'] == 'admin') {
        if (in_array($pagina, $adminNavs)) {
            includeData($pagina, 'admin');
        } else {
            includeError404();
        }
    } elseif ($_SESSION['role'] == 'moderador') {
        if (in_array($pagina, $moderadorNavs)) {
            includeData($pagina, 'moderador');
        } else {
            includeError404();
        }
    } else {
        if (in_array($pagina, $userNavs)) {
            includeData($pagina, 'user');
        } else {
            includeError404();
        }
    }
} elseif ($pagina == 'Login') {
    include "view/pages/auth/login.php";
} else {
    header("Location: Login");
    exit();
}

function includeError404()
{
    include 'error404.php';
}

function includeData($pagina, $role)
{
    require 'view/css.php';
    require 'view/navs/navbar.php';
    require 'view/navs/sidenav.php';
    require "view/pages/$role/$pagina.php";
    require 'view/js.php';
    echo "
    <footer>
        <p>&copy; 2025 Gestión de Usuarios</p>
    </footer>
    ";
}