<?php

switch ($_POST["action"]) {
    case 'newUser':
        newUsers();
        break;
    case 'getUsers':
        getUsers();
        break;
    case 'login':
        login();
        break;
    case 'getUser':
        getUser();
        break;
    case 'editUser':
        editUser();
        break;
    case 'deleteUser':
        deleteUser();
        break;
}

function newUsers() {
    // Encriptar datos después de validarlos
    $nombre   = SecureVault::encryptData($_POST['nombre'], 'name');
    $apellidos = SecureVault::encryptData($_POST['apellidos'], 'name');
    $email    = SecureVault::encryptData($_POST['email'], 'email');
    
    $password = SecureVault::encryptData($_POST['password'], 'password');

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $role     = SecureVault::encryptData($_POST['role'], 'role');

    $data = array(
        'nombre'=> $nombre,
        'apellidos'=> $apellidos,
        'email'=> $email,
        'password'=> $passwordHash,
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

function login() {
    $email    = SecureVault::encryptData($_POST['email'], 'email');
    $password = SecureVault::encryptData($_POST['password'], 'password');

    $item = 'email';
    $searchUser = FormsController::ctrSearchUser($item, $email);
    if ($searchUser) {
        if (password_verify($password, $searchUser['password'])) {
            session_start();
            $_SESSION["logged"] = true;
            $_SESSION["idUser"] = $searchUser["id"];
            $_SESSION["nombre"] = SecureVault::decryptData($searchUser["nombre"]);
            $_SESSION["apellidos"] = SecureVault::decryptData($searchUser["apellidos"]);
            $_SESSION["email"] = SecureVault::decryptData($searchUser["email"]);
            $_SESSION["role"] = SecureVault::decryptData($searchUser["role"]);
            echo json_encode(['success'=> true, 'message'=> 'Login Correcto']);
        } else {
            echo json_encode(['success'=> false, 'message'=> 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['success'=> false, 'message'=> 'Error inesperado']);
    }
}

function getUsers() {
    $users = FormsController::ctrGetUsers();
    $data = [];
    foreach ($users as $key => $user) {
        $data[] = [
            'id' => $user['id'],
            'nombre' => SecureVault::decryptData($user['nombre']),
            'apellidos' => SecureVault::decryptData($user['apellidos']),
            'email' => SecureVault::decryptData($user['email']),
            'role' => SecureVault::decryptData($user['role'])
        ];
    }
    echo json_encode($data);
}

function getUser() {
    $id = $_POST['userId'];
    $user = FormsController::ctrGetUser($id);
    $data = [
        'id' => $user['id'],
        'nombre' => SecureVault::decryptData($user['nombre']),
        'apellidos' => SecureVault::decryptData($user['apellidos']),
        'email' => SecureVault::decryptData($user['email']),
        'role' => SecureVault::decryptData($user['role'])
    ];
    
    // json message success and data
    echo json_encode(['success'=> true, 'data'=> $data]);
}

function editUser() {
    $id       = $_POST['id'];
    $nombre   = SecureVault::encryptData($_POST['nombre'], 'name');
    $apellidos = SecureVault::encryptData($_POST['apellidos'], 'name');
    $email    = SecureVault::encryptData($_POST['email'], 'email');
    $role     = SecureVault::encryptData($_POST['role'], 'role');
    $data = array(
        'id'=> $id,
        'nombre'=> $nombre,
        'apellidos'=> $apellidos,
        'email'=> $email,
        'role'=> $role
    );
    $editUser = FormsController::ctrEditUser($data);
    if ($editUser == 'ok') {
        echo json_encode(['success'=> true, 'message'=> 'Usuario Editado']);
    } else {
        echo json_encode(['success'=> false, 'message' => $editUser]);
    }
}

function deleteUser() {
    $id = $_POST['userId'];
    $deleteUser = FormsController::ctrDeleteUser($id);
    if ($deleteUser == 'ok') {
        echo json_encode(['success'=> true, 'message'=> 'Usuario Eliminado']);
    } else {
        echo json_encode(['success'=> false, 'message' => $deleteUser]);
    }
}