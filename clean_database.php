<?php
// Function to check if MySQL is running
function isMySQLRunning() {
    $conn = @mysqli_connect('localhost', 'root', '');
    return $conn !== false;
}

// Only proceed if MySQL is running
if (isMySQLRunning()) {
    // Remove tablespace files
    $files_to_remove = [
        'C:\xampp\mysql\data\itinerary_system\admin.ibd',
        'C:\xampp\mysql\data\itinerary_system\guides.ibd',
        'C:\xampp\mysql\data\itinerary_system\itineraries.ibd',
        'C:\xampp\mysql\data\itinerary_system\guide_assignments.ibd'
    ];

    foreach($files_to_remove as $file) {
        if(file_exists($file)) {
            unlink($file);
            echo "Removed file: $file<br>";
        }
    }

    echo "Database cleanup completed. Please run setup_database.php now.";
} else {
    echo "MySQL is not running. Please start MySQL from XAMPP Control Panel first.";
}
?> 