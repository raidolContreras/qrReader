<?php

session_start();

// Validar y obtener la página solicitada
$pagina = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_STRING);
$pagina = $pagina ? $pagina : 'inicio';

    includeUserPages($pagina);
function includeUserPages($pagina) {
    include 'view/pages/' . $pagina . '.php';
}

// Función para incluir componentes comunes
function includeCommonComponents() {
    include 'view/pages/navs/sidebar.php';
}

// Función para incluir página de error 404
function includeError404() {
    include 'error404.php';
}