<?php
$dir = __DIR__ . '/../vendor/autoload.php';
require $dir;

class FormsController
{
    static public function ctrNewUsers($data)
    {
        $result = FormsModel::mdlNewUsers($data);
        return $result;
    }

    static public function ctrSearchUser($item, $value)
    {
        $result = FormsModel::mdlSearchUser($item, $value);
        return $result;
    }

    static public function ctrGetUsers()
    {
        $result = FormsModel::mdlGetUsers();
        return $result;
    }

    static public function ctrGetUsersByRoute()
    {
        $result = FormsModel::mdlGetUsers();
        $users = [];
        foreach ($result as $user) {
            if (SecureVault::decryptData($user['role']) != 'admin') {
                $users[] = [
                    'id' => $user['id'],
                    'nombre' => SecureVault::decryptData($user['nombre']),
                    'apellidos' => SecureVault::decryptData($user['apellidos']),
                    'email' => SecureVault::decryptData($user['email']),
                    'role' => SecureVault::decryptData($user['role']),
                    'isActive' => $user['isActive'],
                ];
            }
        }
        return $users;
    }

    static public function ctrGetUser($id)
    {
        $result = FormsModel::mdlGetUser($id);
        return $result;
    }

    static public function ctrEditUser($data)
    {
        $result = FormsModel::mdlEditUser($data);
        return $result;
    }

    static public function ctrSuspendUser($id)
    {
        $result = FormsModel::mdlSuspendUser($id);
        return $result;
    }

    static public function ctrReactivateUser($id)
    {
        $result = FormsModel::mdlReactivateUser($id);
        return $result;
    }

    static public function ctrDeleteUser($id)
    {
        $result = FormsModel::mdlDeleteUser($id);
        return $result;
    }

    static public function ctrResetPassword($data)
    {
        $result = FormsModel::mdlResetPassword($data);
        return $result;
    }

    static public function ctrGetRoutes()
    {
        // 1) Traemos el array plano
        $flat = FormsModel::mdlGetUsersByRoute();

        // 2) Lo agrupamos por ruta
        $grouped = [];
        foreach ($flat as $row) {
            $id = $row['idRoute'];

            // Si aún no existe la ruta, la inicializamos
            if (!isset($grouped[$id])) {
                $grouped[$id] = [
                    'idRoute'   => $row['idRoute'],
                    'nameRoute' => $row['nameRoute'],
                    'registerType' => $row['registerType'],
                    'isActive'  => (int) $row['isActive'],
                    'created_at' => $row['created_at'],
                    'users'     => []
                ];
            }

            // Añadimos el usuario descifrado al array de usuarios
            $grouped[$id]['users'][] = [
                'id'        => $row['userId'],
                'nombre'    => SecureVault::decryptData($row['nombre']),
                'apellidos' => SecureVault::decryptData($row['apellidos']),
                'role'      => SecureVault::decryptData($row['role']),
            ];
        }

        // 3) Reindexamos para que sea un array numérico
        $routes = array_values($grouped);

        // 4) Devolvemos el JSON con la estructura esperada
        return [
            'success' => true,
            'data'    => $routes
        ];
    }

    static public function ctrGetRoutesToScan($idUser)
    {
        $result = FormsModel::mdlGetUsersByRouteOrRoutesByUser($idUser, null);
        return $result;
    }

    static public function ctrGetRoute($idRoute)
    {
        $result = FormsModel::mdlGetRoute($idRoute);
        $result['users'] = FormsModel::mdlGetUsersByRouteOrRoutesByUser(null, $idRoute);
        foreach ($result['users'] as $key => $user) {
            $result['users'][$key]['nombre'] = SecureVault::decryptData($user['nombre']);
            $result['users'][$key]['apellidos'] = SecureVault::decryptData($user['apellidos']);
            $result['users'][$key]['role'] = SecureVault::decryptData($user['role']);
        }
        return $result;
    }

    static public function ctrNewRoute($nameRoute, $registerType)
    {
        $result = FormsModel::mdlNewRoute($nameRoute, $registerType);
        return $result;
    }

    static public function ctrAssignUserToRoute($idUser, $idRoute)
    {
        $result = FormsModel::mdlAssignUserToRoute($idUser, $idRoute);
        return $result;
    }

    static public function ctrUpdateRouteUsers($idRoute, $encargados)
    {
        $result = FormsModel::mdlUpdateRouteUsers($idRoute, $encargados);
        return $result;
    }

    static public function ctrEditRoute($idRoute, $nameRoute, $registerType)
    {
        $result = FormsModel::mdlEditRoute($idRoute, $nameRoute, $registerType);
        return $result;
    }

    static public function ctrDeleteRoute($idRoute)
    {
        $result = FormsModel::mdlDeleteRoute($idRoute);
        return $result;
    }

    static public function ctrRegisterStudent($data, $idUser, $route)
    {
        $result = FormsModel::mdlRegisterStudent($data, $idUser, $route);
        return $result;
    }

    static public function ctrGetStats()
    {
        $result = FormsModel::mdlGetStats();
        return $result;
    }

    static public function ctrGetStatsByScans()
    {
        $result = FormsModel::mdlGetStatsByScans();
        return $result;
    }

    static public function ctrGetStatsByRoutes()
    {
        $result = FormsModel::mdlGetStatsByRoutes();
        return $result;
    }

    static public function ctrGetLogsScans()
    {
        $result = FormsModel::mdlGetLogsScans();
        return $result;
    }
}

class SecureVault
{
    static public function generateSecretKey()
    {
        $masterKey = bin2hex(random_bytes(32));
        return $masterKey;
    }

    static public function encryptData($data, $tipo = 'name')
    {
        if (self::validarDato($data, $tipo)) {
            if (!getenv('MASTER_KEY')) {
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
                $dotenv->load();
            }

            $key = $_ENV['MASTER_KEY'] ?? $_SERVER['MASTER_KEY'] ?? null;
            if (!$key) {
                throw new Exception("La clave MASTER_KEY no está definida.");
            }

            // Usar un IV fijo (no recomendado para producción)
            $iv = str_repeat("0", 16);
            $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
            return base64_encode($encrypted);
        } else {
            self::responseError("El dato no es válido.");
        }
    }

    static public function decryptData($data)
    {
        // Cargar .env solo si no está cargado previamente
        if (!isset($_ENV['MASTER_KEY']) && !isset($_SERVER['MASTER_KEY'])) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        }

        $key = $_ENV['MASTER_KEY'] ?? $_SERVER['MASTER_KEY'] ?? null;
        if (!$key) {
            throw new Exception("La clave MASTER_KEY no está definida.");
        }

        $encrypted = base64_decode($data); // Decodificar la cadena en Base64
        // Usar el mismo IV fijo que en la encriptación
        $iv = str_repeat("0", 16);

        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }

    static private function validarDato($dato, $tipo)
    {
        $dato = trim($dato); // Eliminar espacios en blanco

        switch ($tipo) {
            case 'name':
                $errores = [];

                // Validación: El campo no puede estar vacío
                if (empty($dato)) {
                    $errores[] = "El campo no puede estar vacío.";
                }

                // Validación: Solo se permiten letras y espacios
                // Esto evita números y símbolos extraños.
                if (!empty($dato) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $dato)) {
                    $errores[] = "El campo no puede contener números o símbolos extraños, pero puede incluir acentos, eñes y diéresis.";
                }

                // Si se detectan errores, se envía una respuesta JSON con todos los mensajes
                if (!empty($errores)) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'errors' => $errores
                    ]);
                    exit;
                }
                break;

            case 'email':
                if (!filter_var($dato, FILTER_VALIDATE_EMAIL)) {
                    self::responseError("Correo electrónico no válido.");
                }
                break;

            case 'password':
                $errores = [];

                // Verifica que la contraseña tenga al menos 8 caracteres
                if (strlen($dato) < 8) {
                    $errores[] = "La contraseña debe tener al menos 8 caracteres.";
                }

                // Verifica que contenga al menos una letra mayúscula
                if (!preg_match('/[A-Z]/', $dato)) {
                    $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
                }

                // Verifica que contenga al menos una letra minúscula
                if (!preg_match('/[a-z]/', $dato)) {
                    $errores[] = "La contraseña debe contener al menos una letra minúscula.";
                }

                // Verifica que contenga al menos un número
                if (!preg_match('/[0-9]/', $dato)) {
                    $errores[] = "La contraseña debe contener al menos un número.";
                }

                // Verifica que contenga al menos un símbolo
                if (!preg_match('/[^A-Za-z0-9]/', $dato)) {
                    $errores[] = "La contraseña debe contener al menos un símbolo.";
                }

                // Si existen errores, se envían todos
                if (!empty($errores)) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'errors' => $errores
                    ]);
                    exit;
                }

                break;

            case 'role':
                $rolesPermitidos = ['admin', 'coordinador', 'chofer'];
                if (!in_array($dato, $rolesPermitidos)) {
                    self::responseError("El rol no es válido.");
                }
                break;

            default:
                self::responseError("Tipo de validación no válido.");
        }

        return $dato;
    }

    // Función para enviar errores y detener la ejecución
    static private function responseError($mensaje)
    {
        echo json_encode(["status" => "error", "message" => $mensaje]);
        exit;
    }
}
