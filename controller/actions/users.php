<?php

switch ($_POST["action"]) {
    case 'newUser':
        newUsers();
}

function newUsers() {
    // Encriptar datos después de validarlos
    $nombre   = SecureVault::encryptData($_POST['nombre'], 'name');
    $apellidos = SecureVault::encryptData($_POST['apellidos'], 'name');
    $email    = SecureVault::encryptData($_POST['email'], 'email');
    $password = SecureVault::encryptData($_POST['password'], 'password');
    $role     = SecureVault::encryptData($_POST['role'], 'role');

    $data = array(
        'nombre'=> $nombre,
        'apellidos'=> $apellidos,
        'email'=> $email,
        'password'=> $password,
        'role'=> $role
    );
    
    // Guardar datos en la base de datos
    $saveUsers = FormsController::ctrNewUsers($data);
    if ($saveUsers == 'ok') {
        echo json_encode(['status'=> 'success', 'message'=> 'Usuario Creado']);
    } else {
        echo json_encode(['status'=> 'error', 'message' => $saveUsers]) ;
    }
}