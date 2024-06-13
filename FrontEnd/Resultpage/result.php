<?php
// Start the session
session_start();

// Retrieve session variables for name, prediction result, and image
$name = $_SESSION['pname'] ?? '';
$prediction_result = $_SESSION['prediction'] ?? '';
$image_data = $_SESSION['image'] ?? '';

// Check if data is available
if (!empty($name) && !empty($prediction_result) && !empty($image_data)) {
    // Prepare a response array with the data
    $response = [
        'name' => $name,
        'prediction_result' => $prediction_result,
        'image' => $image_data
    ];
} else {
    // Return an error response if data is not available
    $response = [
        'error' => 'No data available'
    ];
}

// Clear session variables after displaying the data
session_unset();
session_destroy();

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
