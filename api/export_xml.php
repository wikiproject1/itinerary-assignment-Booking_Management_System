<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get request parameters
$type = $_POST['type'] ?? 'itineraries';
$dateRange = $_POST['date_range'] ?? 'month';
$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;

// Calculate date range
$dateCondition = '';
switch($dateRange) {
    case 'today':
        $dateCondition = "DATE(created_at) = CURDATE()";
        $dateRangeText = date('Y-m-d');
        break;
    case 'week':
        $dateCondition = "YEARWEEK(created_at) = YEARWEEK(CURDATE())";
        $dateRangeText = date('Y-m-d', strtotime('monday this week')) . ' to ' . date('Y-m-d', strtotime('sunday this week'));
        break;
    case 'month':
        $dateCondition = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $dateRangeText = date('Y-m-01') . ' to ' . date('Y-m-t');
        break;
    case 'quarter':
        $dateCondition = "QUARTER(created_at) = QUARTER(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $quarter = ceil(date('n') / 3);
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $startMonth + 2;
        $dateRangeText = date("Y-$startMonth-01") . ' to ' . date("Y-$endMonth-t");
        break;
    case 'year':
        $dateCondition = "YEAR(created_at) = YEAR(CURDATE())";
        $dateRangeText = date('Y-01-01') . ' to ' . date('Y-12-31');
        break;
    case 'custom':
        if ($startDate && $endDate) {
            $dateCondition = "DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
            $dateRangeText = "$startDate to $endDate";
        } else {
            $dateCondition = "1=1";
            $dateRangeText = "All Time";
        }
        break;
    default:
        $dateCondition = "1=1";
        $dateRangeText = "All Time";
}

try {
    // Create XML document
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    // Add Excel XML namespace
    $root = $xml->createElementNS('urn:schemas-microsoft-com:office:spreadsheet', 'ss:Workbook');
    $xml->appendChild($root);

    // Add Excel specific namespaces
    $root->setAttribute('xmlns:x', 'urn:schemas-microsoft-com:office:excel');
    $root->setAttribute('xmlns:ss', 'urn:schemas-microsoft-com:office:spreadsheet');
    $root->setAttribute('xmlns:html', 'http://www.w3.org/TR/REC-html40');

    // Create Worksheet
    $worksheet = $xml->createElement('ss:Worksheet');
    $worksheet->setAttribute('ss:Name', ucfirst($type) . ' Report');
    $root->appendChild($worksheet);

    // Create Table
    $table = $xml->createElement('ss:Table');
    $worksheet->appendChild($table);

    // Add metadata row
    $row = $xml->createElement('ss:Row');
    $table->appendChild($row);

    $cell = $xml->createElement('ss:Cell');
    $row->appendChild($cell);
    $data = $xml->createElement('ss:Data', 'Report: ' . ucfirst($type) . ' Report');
    $data->setAttribute('ss:Type', 'String');
    $cell->appendChild($data);

    $row = $xml->createElement('ss:Row');
    $table->appendChild($row);

    $cell = $xml->createElement('ss:Cell');
    $row->appendChild($cell);
    $data = $xml->createElement('ss:Data', 'Date Range: ' . $dateRangeText);
    $data->setAttribute('ss:Type', 'String');
    $cell->appendChild($data);

    $row = $xml->createElement('ss:Row');
    $table->appendChild($row);

    $cell = $xml->createElement('ss:Cell');
    $row->appendChild($cell);
    $data = $xml->createElement('ss:Data', 'Generated At: ' . date('Y-m-d H:i:s'));
    $data->setAttribute('ss:Type', 'String');
    $cell->appendChild($data);

    // Add empty row
    $row = $xml->createElement('ss:Row');
    $table->appendChild($row);

    // Add headers
    $headers = [];
    switch($type) {
        case 'itineraries':
            $headers = ['Month', 'Group Name', 'Locations', 'Arrival Time', 'Departure Time', 'Trip Types', 'Status', 'Amount', 'Notes'];
            break;
        case 'guides':
            $headers = ['First Name', 'Last Name', 'Phone Number', 'Email', 'Car Plate Number', 'Status'];
            break;
        case 'assignments':
            $headers = ['Guide Name', 'Itinerary Group', 'Assignment Date', 'Status', 'Notes'];
            break;
        case 'financial':
            $headers = ['Month', 'Group Name', 'Amount', 'Status', 'Guide', 'Assignment Date'];
            break;
    }

    $row = $xml->createElement('ss:Row');
    $table->appendChild($row);
    foreach($headers as $header) {
        $cell = $xml->createElement('ss:Cell');
        $row->appendChild($cell);
        $data = $xml->createElement('ss:Data', $header);
        $data->setAttribute('ss:Type', 'String');
        $cell->appendChild($data);
    }

    // Add data rows
    switch($type) {
        case 'itineraries':
            $sql = "SELECT * FROM itineraries WHERE $dateCondition ORDER BY created_at DESC";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()) {
                $dataRow = $xml->createElement('ss:Row');
                $table->appendChild($dataRow);

                $fields = ['month', 'group_name', 'locations', 'arrival_time', 'departure_time', 'trip_types', 'completion_status', 'amount', 'notes'];
                foreach($fields as $field) {
                    $cell = $xml->createElement('ss:Cell');
                    $dataRow->appendChild($cell);
                    $data = $xml->createElement('ss:Data', htmlspecialchars($row[$field] ?? '', ENT_XML1, 'UTF-8'));
                    $data->setAttribute('ss:Type', 'String');
                    $cell->appendChild($data);
                }
            }
            break;

        case 'guides':
            $sql = "SELECT * FROM guides WHERE $dateCondition ORDER BY created_at DESC";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()) {
                $dataRow = $xml->createElement('ss:Row');
                $table->appendChild($dataRow);

                $fields = ['first_name', 'last_name', 'phone_number', 'email', 'car_plate_number', 'status'];
                foreach($fields as $field) {
                    $cell = $xml->createElement('ss:Cell');
                    $dataRow->appendChild($cell);
                    $data = $xml->createElement('ss:Data', htmlspecialchars($row[$field] ?? '', ENT_XML1, 'UTF-8'));
                    $data->setAttribute('ss:Type', 'String');
                    $cell->appendChild($data);
                }
            }
            break;

        case 'assignments':
            $sql = "SELECT ga.*, g.first_name as guide_first_name, g.last_name as guide_last_name, 
                    i.group_name as itinerary_group_name 
                    FROM guide_assignments ga
                    JOIN guides g ON ga.guide_id = g.id
                    JOIN itineraries i ON ga.itinerary_id = i.id
                    WHERE $dateCondition
                    ORDER BY ga.created_at DESC";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()) {
                $dataRow = $xml->createElement('ss:Row');
                $table->appendChild($dataRow);

                $fields = ['guide_first_name', 'guide_last_name', 'itinerary_group_name', 'assignment_date', 'status', 'notes'];
                foreach($fields as $field) {
                    $cell = $xml->createElement('ss:Cell');
                    $dataRow->appendChild($cell);
                    $data = $xml->createElement('ss:Data', htmlspecialchars($row[$field] ?? '', ENT_XML1, 'UTF-8'));
                    $data->setAttribute('ss:Type', 'String');
                    $cell->appendChild($data);
                }
            }
            break;

        case 'financial':
            $sql = "SELECT i.month, i.group_name, i.amount, i.completion_status, 
                    CONCAT(g.first_name, ' ', g.last_name) as guide_name,
                    ga.assignment_date
                    FROM itineraries i
                    LEFT JOIN guide_assignments ga ON i.id = ga.itinerary_id
                    LEFT JOIN guides g ON ga.guide_id = g.id
                    WHERE $dateCondition
                    ORDER BY i.created_at DESC";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()) {
                $dataRow = $xml->createElement('ss:Row');
                $table->appendChild($dataRow);

                $fields = ['month', 'group_name', 'amount', 'completion_status', 'guide_name', 'assignment_date'];
                foreach($fields as $field) {
                    $cell = $xml->createElement('ss:Cell');
                    $dataRow->appendChild($cell);
                    $data = $xml->createElement('ss:Data', htmlspecialchars($row[$field] ?? '-', ENT_XML1, 'UTF-8'));
                    $data->setAttribute('ss:Type', 'String');
                    $cell->appendChild($data);
                }
            }
            break;
    }

    // Clear any previous output
    ob_clean();
    
    // Set headers for download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.xml"');
    header('Cache-Control: max-age=0');
    
    // Output XML
    echo $xml->saveXML();

} catch(Exception $e) {
    // Clear any previous output
    ob_clean();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 