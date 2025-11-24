<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT * FROM itineraries WHERE id = '$id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Itinerary not found']);
            }
        } else {
            $sql = "SELECT * FROM itineraries ORDER BY id DESC";
            $result = $conn->query($sql);
            $itineraries = [];
            while ($row = $result->fetch_assoc()) {
                $itineraries[] = $row;
            }
            echo json_encode($itineraries);
        }
        break;

    case 'POST':
        // Get the raw POST data
        $raw_data = file_get_contents('php://input');
        
        // Try to decode as JSON first
        $data = json_decode($raw_data, true);
        
        // If not JSON, try to parse as form data
        if (!$data) {
            parse_str($raw_data, $data);
        }
        
        // Validate required fields
        $required_fields = [
            'month', 'group_name', 'starting_location', 'final_destination',
            'arrival_time', 'departure_time', 'total_amount'
        ];
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                exit;
            }
        }
        
        // Process status array if present
        if (isset($data['status']) && is_array($data['status'])) {
            $data['status'] = implode(', ', $data['status']);
        }
        
        // Map itinerary_status to completion_status
        if (isset($data['itinerary_status'])) {
            $data['completion_status'] = $data['itinerary_status'];
            unset($data['itinerary_status']);
        }
        
        // Set default values for optional fields
        $data['notes'] = $data['notes'] ?? '';
        $data['safari_days'] = $data['safari_days'] ?? '';
        $data['deposit_amount'] = $data['deposit_amount'] ?? '0';
        $data['remaining_amount'] = $data['remaining_amount'] ?? $data['total_amount'];
        
        // Determine trip type based on status
        if (strpos($data['status'], 'Safari') !== false) {
            $data['trip_type'] = 'Safari';
        } elseif (strpos($data['status'], 'Kilimanjaro Climbing') !== false) {
            $data['trip_type'] = 'Kilimanjaro';
        } elseif (strpos($data['status'], 'Day Trip') !== false) {
            $data['trip_type'] = 'Day Trip';
        } elseif (strpos($data['status'], 'Zanzibar') !== false) {
            $data['trip_type'] = 'Zanzibar';
        } else {
            $data['trip_type'] = 'Other';
        }
        
        // Prepare SQL statement
        $fields = array_keys($data);
        $values = array_map(function($value) use ($conn) {
            return "'" . $conn->real_escape_string($value) . "'";
        }, array_values($data));
        
        $sql = "INSERT INTO itineraries (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $values) . ")";
        
        if ($conn->query($sql)) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Itinerary created successfully', 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating itinerary: ' . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input data or missing ID']);
            break;
        }
        
        $id = $conn->real_escape_string($data['id']);
        unset($data['id']);
        
        // Map itinerary_status to completion_status if present
        if (isset($data['itinerary_status'])) {
            $data['completion_status'] = $data['itinerary_status'];
            unset($data['itinerary_status']);
        }
        
        $updates = [];
        foreach ($data as $key => $value) {
            if ($value !== null) { // Only update fields that are provided
                $updates[] = "$key = '" . $conn->real_escape_string($value) . "'";
            }
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No fields to update']);
            break;
        }
        
        $sql = "UPDATE itineraries SET " . implode(", ", $updates) . " WHERE id = '$id'";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Itinerary updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating itinerary: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing ID parameter']);
            break;
        }
        
        $id = $conn->real_escape_string($_GET['id']);
        $sql = "DELETE FROM itineraries WHERE id = '$id'";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Itinerary deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting itinerary: ' . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

$conn->close();
?> 