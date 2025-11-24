<?php
require_once "config/database.php";

// First, drop all existing tables
$drop_tables = [
    "DROP TABLE IF EXISTS guide_assignments",
    "DROP TABLE IF EXISTS guides",
    "DROP TABLE IF EXISTS itineraries",
    "DROP TABLE IF EXISTS admin"
];

// Execute drop table queries
foreach($drop_tables as $sql) {
    if(mysqli_query($conn, $sql)) {
        echo "Table dropped successfully<br>";
    } else {
        echo "Error dropping table: " . mysqli_error($conn) . "<br>";
    }
}

// Array of table creation SQL statements
$tables = [
    "CREATE TABLE admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB",
    
    "CREATE TABLE guides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        phone_number VARCHAR(20),
        email VARCHAR(100),
        car_plate_number VARCHAR(20),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "CREATE TABLE itineraries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        month VARCHAR(20) NOT NULL,
        group_name VARCHAR(255) NOT NULL,
        starting_location VARCHAR(255) NOT NULL,
        final_destination VARCHAR(255) NOT NULL,
        arrival_time DATETIME NOT NULL,
        departure_time DATETIME NOT NULL,
        status VARCHAR(255),
        completion_status ENUM('pending', 'completed') DEFAULT 'pending',
        total_amount DECIMAL(10,2) NOT NULL,
        deposit_amount DECIMAL(10,2) DEFAULT 0,
        remaining_amount DECIMAL(10,2) DEFAULT 0,
        notes TEXT,
        safari_days VARCHAR(255),
        guide_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE SET NULL
    ) ENGINE=InnoDB",
    
    "CREATE TABLE guide_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guide_id INT,
        itinerary_id INT,
        assignment_date DATE,
        status ENUM('assigned', 'completed', 'cancelled') DEFAULT 'assigned',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE SET NULL,
        FOREIGN KEY (itinerary_id) REFERENCES itineraries(id) ON DELETE SET NULL
    ) ENGINE=InnoDB"
];

// Execute each table creation query
foreach($tables as $sql) {
    if(mysqli_query($conn, $sql)) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . mysqli_error($conn) . "<br>";
    }
}

// Create admin user
$username = "admin";
$password = "admin123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
    if(mysqli_stmt_execute($stmt)) {
        echo "Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error creating admin user: " . mysqli_error($conn) . "<br>";
    }
    mysqli_stmt_close($stmt);
}

// Insert sample data
$sample_data = [
    "INSERT INTO guides (name, contact_number, email) VALUES 
    ('John Doe', '1234567890', 'john@example.com'),
    ('Jane Smith', '0987654321', 'jane@example.com')",
    
    "INSERT INTO itineraries (title, description, start_date, end_date) VALUES 
    ('Summer Tour 2024', 'Summer vacation package', '2024-06-01', '2024-06-15'),
    ('Winter Special', 'Winter holiday package', '2024-12-20', '2024-12-31')",
    
    "INSERT INTO guide_assignments (guide_id, itinerary_id, assignment_date) VALUES 
    (1, 1, '2024-06-01'),
    (2, 2, '2024-12-20')"
];

// Execute sample data insertion
foreach($sample_data as $sql) {
    if(mysqli_query($conn, $sql)) {
        echo "Sample data inserted successfully<br>";
    } else {
        echo "Error inserting sample data: " . mysqli_error($conn) . "<br>";
    }
}

mysqli_close($conn);
echo "<br>Database setup completed!";
?> 