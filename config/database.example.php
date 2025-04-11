<?php
// Database configuration
$db_host = "localhost";
$db_user = "your_username";
$db_pass = "your_password";
$db_name = "itinerary_system";

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");
?> 