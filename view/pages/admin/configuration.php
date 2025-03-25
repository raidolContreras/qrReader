<?php
// Ruta del archivo .env
$envFile = dirname(__DIR__, 3) . '/.env';
$envVars = [];

if (!file_exists($envFile)) {
    echo "No se encontró el archivo .env en la ruta: $envFile";
    exit;
}

// Verificamos que el archivo exista y lo leemos
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignoramos comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Separamos la clave y el valor (limitamos a 2 partes)
        list($key, $value) = explode('=', $line, 2);
        $envVars[trim($key)] = trim($value);
    }
}

// Eliminamos la variable MASTER_KEY para que no se muestre en el formulario
if (isset($envVars['MASTER_KEY'])) {
    unset($envVars['MASTER_KEY']);
}
?>
<div class="row justify-content-center mt-5 mx-0">
    <div class="content col-12 col-md-6 col-lg-7 col-xl-4">
        <h2 class="mb-4">Editar datos de conexión</h2>
        <form id="envForm" class="card p-4">
            <?php foreach ($envVars as $key => $value): ?>
                <div class="form-group">
                    <label for="<?php echo $key; ?>"><?php echo $key; ?>:</label>
                    <input type="text" class="form-control" name="<?php echo $key; ?>" id="<?php echo $key; ?>"
                        value="<?php echo htmlspecialchars($value); ?>">
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        </form>
        <div id="result" class="mt-3"></div>
    </div>
</div>
<script>
    $(document).ready(function () {
        // Interceptamos el envío del formulario
        $("#envForm").submit(function (e) {
            e.preventDefault(); // Prevenimos el envío normal del formulario
            let formData = $(this).serialize();
            // Agregamos la acción para actualizar el archivo
            formData += "&action=editENV";

            // Realizamos la llamada AJAX
            $.ajax({
                url: 'controller/selectAction.php',
                type: 'POST',
                data: formData,
                dataType: 'json',  // Esperamos una respuesta en JSON
                success: function (response) {
                    if (response.success) {
                        $("#result").html('<span class="text-success">' + response.message + '</span>');
                    } else {
                        $("#result").html('<span class="text-danger">' + response.message + '</span>');
                    }
                },
                error: function () {
                    $("#result").html('<span class="text-danger">Ocurrió un error al actualizar el archivo.</span>');
                }
            });
        });
    });
</script>