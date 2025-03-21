$(document).ready(function(){
    $("#installForm").on("submit", function(e){
        e.preventDefault();
    });
    
    $("#installForm").on("submit", function(e) {
        // Obtenemos el valor del campo de la contraseña
        var password = $("#admin_password").val();
        var errors = [];

        // Verifica que la contraseña tenga al menos 8 caracteres
        if (password.length < 8) {
            errors.push("La contraseña debe tener al menos 8 caracteres.");
        }

        // Verifica que contenga al menos una letra mayúscula
        if (!/[A-Z]/.test(password)) {
            errors.push("La contraseña debe contener al menos una letra mayúscula.");
        }

        // Verifica que contenga al menos una letra minúscula
        if (!/[a-z]/.test(password)) {
            errors.push("La contraseña debe contener al menos una letra minúscula.");
        }

        // Verifica que contenga al menos un número
        if (!/[0-9]/.test(password)) {
            errors.push("La contraseña debe contener al menos un número.");
        }

        // Verifica que contenga al menos un símbolo
        if (!/[^A-Za-z0-9]/.test(password)) {
            errors.push("La contraseña debe contener al menos un símbolo.");
        }

        // Si se encontraron errores, se previene el envío y se muestran
        if (errors.length > 0) {
            e.preventDefault();
            $("#errorContainer").html(errors.join("<br>"));
        } else {
            $("#errorContainer").html("");
            e.preventDefault();

            // Reiniciamos mensajes y deshabilitamos el botón
            $("#message").html("");
            $("#btnSubmit").prop("disabled", true).text("Instalando...");
    
            $.ajax({
                url: 'backend/process_install.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        $("#message").html("<p class='success'>" + response.message + "</p>")
                                    .hide().fadeIn("slow");
                        // Redirigir después de 2 segundos
                        setTimeout(function(){
                            window.location.href = "./";
                        }, 2000);
                    } else {
                        $("#message").html("<p class='error'>" + response.message + "</p>")
                                    .hide().fadeIn("slow");
                        $("#btnSubmit").prop("disabled", false).text("Instalar");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    $("#message").html("<p class='error'>Error en el servidor: " + textStatus + "</p>")
                                .hide().fadeIn("slow");
                    $("#btnSubmit").prop("disabled", false).text("Instalar");
                }
            });
        }
    });
});
