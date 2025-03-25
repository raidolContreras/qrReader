<?php
// logout
session_start();
session_destroy();

// Redirect to login page
$response = array(
    'status' => 'success',
    'message' => 'Logged out successfully',
    'redirect' => './'
);

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;