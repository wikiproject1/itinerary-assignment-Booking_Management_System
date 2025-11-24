<?php
// Configuration
$base_url = "http://localhost/table/";
$output_dir = __DIR__ . "/screenshots/";
$pages = [
    "login" => "",
    "dashboard" => "dashboard.php",
    "itinerary" => "itinerary.php",
    "guides" => "guides.php",
    "reports" => "reports.php"
];

// Create output directory if it doesn't exist
if (!file_exists($output_dir)) {
    mkdir($output_dir, 0777, true);
}

// Function to capture screenshot using Chrome headless
function capture_screenshot($url, $output_file, $width = 1920, $height = 1080) {
    $chrome_command = sprintf(
        'start chrome --headless --disable-gpu --screenshot="%s" --window-size=%d,%d "%s"',
        $output_file,
        $width,
        $height,
        $url
    );
    
    exec($chrome_command);
    
    // Wait for the screenshot to be saved
    sleep(2);
}

// Capture screenshots for each page
foreach ($pages as $name => $path) {
    $url = $base_url . $path;
    $output_file = $output_dir . $name . ".png";
    
    echo "Capturing {$name}... ";
    capture_screenshot($url, $output_file);
    echo "Done!\n";
}

echo "\nAll screenshots have been captured!\n";
?> 