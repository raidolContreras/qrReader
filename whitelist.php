<?php

session_start();

if (isset($_SESSION['logged'])) {
    // if (!isset($_SESSION['logged'])) {
    $pagina = filter_input(INPUT_GET, 'pagina') ?: 'login';
    if ($pagina == 'login') {
        include_once 'view/pages/auth/login.php';
    } else {
        header("Location: login");
        exit();
    }
} else {
    $pagina = filter_input(INPUT_GET, 'pagina') ?: 'Usuarios';
    $title = "Gestión de Usuarios";
    includeDataAdmin();
    // if ($_SESSION['rol'] == 'admin') {
    // } else if ($_SESSION['rol'] == 'moderator') {
    // } else {
    // }
}

function includeError404()
{
    include 'error404.php';
}

function includeDataAdmin()
{
    require 'view/css.php';
    require 'view/navs/navbar.php';
    require 'view/navs/sidenav.php';
    require 'view/pages/admin/users.php';
    require 'view/js.php';
}
