<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

// Get request parameters
$type = $_GET['type'] ?? 'itineraries';
$dateRange = $_GET['date_range'] ?? 'month';
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

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
    $response = [
        'success' => true,
        'title' => ucfirst($type) . ' Report',
        'date_range' => $dateRangeText,
        'columns' => [],
        'data' => []
    ];

    switch($type) {
        case 'itineraries':
            $response['columns'] = [
                ['title' => 'Month'],
                ['title' => 'Group Name'],
                ['title' => 'Locations'],
                ['title' => 'Arrival Time'],
                ['title' => 'Departure Time'],
                ['title' => 'Trip Types'],
                ['title' => 'Status'],
                ['title' => 'Amount'],
                ['title' => 'Notes']
            ];

            $sql = "SELECT * FROM itineraries WHERE $dateCondition ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Database error: " . $conn->error);
            }

            while($row = $result->fetch_assoc()) {
                $response['data'][] = [
                    $row['month'] ?? '',
                    $row['group_name'] ?? '',
                    $row['locations'] ?? '',
                    $row['arrival_time'] ?? '',
                    $row['departure_time'] ?? '',
                    $row['trip_types'] ?? '',
                    $row['completion_status'] ?? '',
                    $row['amount'] ?? '',
                    $row['notes'] ?? ''
                ];
            }
            break;

        case 'guides':
            $response['columns'] = [
                ['title' => 'First Name'],
                ['title' => 'Last Name'],
                ['title' => 'Phone Number'],
                ['title' => 'Email'],
                ['title' => 'Car Plate Number'],
                ['title' => 'Status']
            ];

            $sql = "SELECT * FROM guides WHERE $dateCondition ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Database error: " . $conn->error);
            }

            while($row = $result->fetch_assoc()) {
                $response['data'][] = [
                    $row['first_name'] ?? '',
                    $row['last_name'] ?? '',
                    $row['phone_number'] ?? '',
                    $row['email'] ?? '',
                    $row['car_plate_number'] ?? '',
                    $row['status'] ?? ''
                ];
            }
            break;

        case 'assignments':
            $response['columns'] = [
                ['title' => 'Guide Name'],
                ['title' => 'Itinerary Group'],
                ['title' => 'Assignment Date'],
                ['title' => 'Status'],
                ['title' => 'Notes']
            ];

            $sql = "SELECT ga.*, g.first_name as guide_first_name, g.last_name as guide_last_name, 
                    i.group_name as itinerary_group_name 
                    FROM guide_assignments ga
                    JOIN guides g ON ga.guide_id = g.id
                    JOIN itineraries i ON ga.itinerary_id = i.id
                    WHERE $dateCondition
                    ORDER BY ga.created_at DESC";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Database error: " . $conn->error);
            }

            while($row = $result->fetch_assoc()) {
                $response['data'][] = [
                    ($row['guide_first_name'] ?? '') . ' ' . ($row['guide_last_name'] ?? ''),
                    $row['itinerary_group_name'] ?? '',
                    $row['assignment_date'] ?? '',
                    $row['status'] ?? '',
                    $row['notes'] ?? ''
                ];
            }
            break;

        case 'financial':
            $response['columns'] = [
                ['title' => 'Month'],
                ['title' => 'Group Name'],
                ['title' => 'Amount'],
                ['title' => 'Status'],
                ['title' => 'Guide'],
                ['title' => 'Assignment Date']
            ];

            $sql = "SELECT i.month, i.group_name, i.amount, i.completion_status, 
                    CONCAT(g.first_name, ' ', g.last_name) as guide_name,
                    ga.assignment_date
                    FROM itineraries i
                    LEFT JOIN guide_assignments ga ON i.id = ga.itinerary_id
                    LEFT JOIN guides g ON ga.guide_id = g.id
                    WHERE $dateCondition
                    ORDER BY i.created_at DESC";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Database error: " . $conn->error);
            }

            while($row = $result->fetch_assoc()) {
                $response['data'][] = [
                    $row['month'] ?? '',
                    $row['group_name'] ?? '',
                    $row['amount'] ?? '',
                    $row['completion_status'] ?? '',
                    $row['guide_name'] ?? '-',
                    $row['assignment_date'] ?? '-'
                ];
            }
            break;
    }

    echo json_encode($response);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 