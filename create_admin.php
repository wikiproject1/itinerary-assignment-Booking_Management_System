<?php
require_once "config/database.php";

// Admin credentials
$username = "admin";
$password = "admin123"; // This will be the password you can use to login

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL statement
$sql = "INSERT INTO admin (username, password) VALUES (?, ?)";

if($stmt = mysqli_prepare($conn, $sql)){
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
    
    // Execute the statement
    if(mysqli_stmt_execute($stmt)){
        echo "Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123";
    } else{
        echo "Error creating admin user: " . mysqli_error($conn);
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?> 