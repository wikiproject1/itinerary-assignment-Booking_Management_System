<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Travel Management System');
$pdf->SetAuthor('Chipmunk Tech');
$pdf->SetTitle('Travel Management System Documentation');

// Set default header data
$pdf->SetHeaderData('', 0, 'Travel Management System Documentation', '');

// Set header and footer fonts
$pdf->setHeaderFont(Array('helvetica', '', 12));
$pdf->setFooterFont(Array('helvetica', '', 8));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Set image scale factor
$pdf->setImageScale(1.25);

// Read the markdown content
$markdown = file_get_contents(__DIR__ . '/user_manual.md');

// Convert markdown to HTML
$parsedown = new Parsedown();
$html = $parsedown->text($markdown);

// Replace image placeholders with actual screenshots
$screenshots_dir = __DIR__ . '/screenshots/';
$images = [
    '![Login Page]' => '<img src="' . $screenshots_dir . 'login.png" alt="Login Page">',
    '![Dashboard]' => '<img src="' . $screenshots_dir . 'dashboard.png" alt="Dashboard">',
    '![Itinerary Management]' => '<img src="' . $screenshots_dir . 'itinerary.png" alt="Itinerary Management">',
    '![Guide Assignment]' => '<img src="' . $screenshots_dir . 'guides.png" alt="Guide Assignment">',
    '![Reports]' => '<img src="' . $screenshots_dir . 'reports.png" alt="Reports">',
    '![Notifications]' => '<img src="' . $screenshots_dir . 'notification.png" alt="Notifications">'
];

// Verify screenshots exist
foreach ($images as $placeholder => $image) {
    $img_path = $screenshots_dir . basename(substr($image, strpos($image, 'src="') + 5, strpos($image, '" alt') - (strpos($image, 'src="') + 5)));
    if (!file_exists($img_path)) {
        echo "Warning: Screenshot not found: " . basename($img_path) . "\n";
        // Use a placeholder message instead of the missing image
        $html = str_replace($placeholder, '<div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: #f8f9fa; text-align: center;">Screenshot will be added: ' . substr($placeholder, 2, -1) . '</div>', $html);
    } else {
        $html = str_replace($placeholder, $image, $html);
    }
}

// Add custom CSS
$html = '
<style>
    body {
        font-family: helvetica;
        line-height: 1.6;
    }
    h1 {
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }
    h2 {
        color: #34495e;
        margin-top: 30px;
    }
    h3 {
        color: #2980b9;
    }
    img {
        max-width: 100%;
        margin: 20px 0;
        border: 1px solid #ddd;
    }
    ul, ol {
        margin-left: 20px;
    }
    .warning {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
</style>
' . $html;

// Add a page
$pdf->AddPage();

// Write HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output(__DIR__ . '/Travel_Management_System_Documentation.pdf', 'F');

echo "PDF documentation has been generated successfully!\n";
?> 