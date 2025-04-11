<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "itinerary_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create admin table if not exists
$sql = "CREATE TABLE IF NOT EXISTS `admin` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "Admin table created/verified successfully<br>";
} else {
    echo "Error creating admin table: " . $conn->error . "<br>";
}

// Check if admin user exists
$check_sql = "SELECT COUNT(*) as count FROM `admin` WHERE `username` = 'admin'";
$result = $conn->query($check_sql);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Insert default admin user with correct password hash (admin123)
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO `admin` (`username`, `password`) VALUES ('admin', ?)";
    
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("s", $password_hash);
    
    if ($stmt->execute()) {
        echo "Default admin user created successfully<br>";
    } else {
        echo "Error creating admin user: " . $stmt->error . "<br>";
    }
    $stmt->close();
} else {
    // Update existing admin password
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $update_sql = "UPDATE `admin` SET `password` = ? WHERE `username` = 'admin'";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $password_hash);
    
    if ($stmt->execute()) {
        echo "Admin password updated successfully<br>";
    } else {
        echo "Error updating admin password: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Create other necessary tables
$tables = [
    "CREATE TABLE IF NOT EXISTS `guides` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `specialization` varchar(100) DEFAULT NULL,
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `itineraries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `status` enum('draft','published','completed','cancelled') NOT NULL DEFAULT 'draft',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `guide_assignments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `guide_id` int(11) NOT NULL,
        `itinerary_id` int(11) NOT NULL,
        `assignment_date` date NOT NULL,
        `status` enum('assigned','completed','cancelled') NOT NULL DEFAULT 'assigned',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `guide_id` (`guide_id`),
        KEY `itinerary_id` (`itinerary_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `groups` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `description` text DEFAULT NULL,
        `size` int(11) NOT NULL,
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `group_assignments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `group_id` int(11) NOT NULL,
        `itinerary_id` int(11) NOT NULL,
        `assignment_date` date NOT NULL,
        `status` enum('assigned','completed','cancelled') NOT NULL DEFAULT 'assigned',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `group_id` (`group_id`),
        KEY `itinerary_id` (`itinerary_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created/verified successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Function to check if a foreign key constraint exists
function foreignKeyExists($conn, $table, $constraint_name) {
    $sql = "SELECT COUNT(*) as count 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = '$table' 
            AND CONSTRAINT_NAME = '$constraint_name' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Add foreign key constraints if they don't exist
$foreign_keys = [
    [
        'table' => 'guide_assignments',
        'constraints' => [
            [
                'name' => 'guide_assignments_ibfk_1',
                'sql' => 'ADD CONSTRAINT `guide_assignments_ibfk_1` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE CASCADE'
            ],
            [
                'name' => 'guide_assignments_ibfk_2',
                'sql' => 'ADD CONSTRAINT `guide_assignments_ibfk_2` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE'
            ]
        ]
    ],
    [
        'table' => 'group_assignments',
        'constraints' => [
            [
                'name' => 'group_assignments_ibfk_1',
                'sql' => 'ADD CONSTRAINT `group_assignments_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE'
            ],
            [
                'name' => 'group_assignments_ibfk_2',
                'sql' => 'ADD CONSTRAINT `group_assignments_ibfk_2` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE'
            ]
        ]
    ]
];

foreach ($foreign_keys as $table_info) {
    $table = $table_info['table'];
    foreach ($table_info['constraints'] as $constraint) {
        if (!foreignKeyExists($conn, $table, $constraint['name'])) {
            $sql = "ALTER TABLE `$table` " . $constraint['sql'];
            if ($conn->query($sql) === TRUE) {
                echo "Foreign key constraint {$constraint['name']} added successfully<br>";
            } else {
                echo "Error adding foreign key constraint {$constraint['name']}: " . $conn->error . "<br>";
            }
        } else {
            echo "Foreign key constraint {$constraint['name']} already exists<br>";
        }
    }
}

echo "Setup completed successfully!<br>";
echo "You can now <a href='index.php'>login</a> with:<br>";
echo "Username: admin<br>";
echo "Password: admin123";

$conn->close();
?> 