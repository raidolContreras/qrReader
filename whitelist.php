<?php

session_start();

if (!isset($_SESSION['logged'])) {
    $title = 'Iniciar sesión';
    $pagina = filter_input(INPUT_GET, 'pagina') ?: 'login';
    if ($pagina == 'login') {
        include_once 'view/pages/auth/login.php';
    } else {
        header("Location: login");
        exit();
    }
} else {
    $pagina = filter_input(INPUT_GET, 'pagina') ?: 'users';
    $title = "Gestión de Usuarios";
    includeDataAdmin($pagina);
    // if ($_SESSION['rol'] == 'admin') {
    // } else if ($_SESSION['rol'] == 'moderator') {
    // } else {
    // }
}

function includeError404()
{
    include 'error404.php';
}

function includeDataAdmin($pagina)
{
    require 'view/css.php';
    require 'view/navs/navbar.php';
    require 'view/navs/sidenav.php';
    require "view/pages/admin/$pagina.php";
    require 'view/js.php';
    echo "
    <footer>
        <p>&copy; 2025 Gestión de Usuarios</p>
    </footer>
    ";
}