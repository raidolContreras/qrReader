$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Get the form data
        var formData = {
            action:'login',
            email: $('#email').val(),
            password: $('#password').val()
        };

        // Send the AJAX request
        $.ajax({
            type: 'POST',
            url: 'controller/selectAction.php', // Replace with your actual login endpoint
            data: formData,
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            if (data.success) {
                window.location.href = './';
            } else {
                alert('Inicio de sesión fallido: ' + data.message);
            }
        })
        .fail(function(data) {
            // Handle errors
            alert('An error occurred. Please try again.');
        });
    });
});