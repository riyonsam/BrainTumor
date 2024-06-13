<?php
// Define your username and password
$valid_username = "admin";
$valid_password = "admin01";

// Retrieve form data
$username = $_POST['username']; // Use POST method for form data retrieval
$password = $_POST['password'];

// Check if username and password match
if ($username === $valid_username && $password === $valid_password) {
    // Credentials are correct, redirect to the next page
    header("Location:home.html");
    exit;
} else {
    // Credentials are incorrect, redirect back to the login page with an error message
    header("Location: login.html?error=1");
    exit;
}
?>
