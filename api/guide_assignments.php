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
            $sql = "SELECT ga.*, g.first_name as guide_first_name, g.last_name as guide_last_name, 
                    i.group_name as itinerary_group_name 
                    FROM guide_assignments ga 
                    JOIN guides g ON ga.guide_id = g.id 
                    JOIN itineraries i ON ga.itinerary_id = i.id 
                    WHERE ga.id = '$id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Assignment not found']);
            }
        } else {
            $sql = "SELECT ga.*, g.first_name as guide_first_name, g.last_name as guide_last_name, 
                    i.group_name as itinerary_group_name 
                    FROM guide_assignments ga 
                    JOIN guides g ON ga.guide_id = g.id 
                    JOIN itineraries i ON ga.itinerary_id = i.id 
                    ORDER BY ga.assignment_date DESC";
            $result = $conn->query($sql);
            $assignments = [];
            while ($row = $result->fetch_assoc()) {
                $assignments[] = $row;
            }
            echo json_encode($assignments);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['guide_id']) || !isset($data['itinerary_id']) || !isset($data['assignment_date'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Guide ID, itinerary ID, and assignment date are required']);
            break;
        }
        
        // Check if guide exists and is active
        $guide_id = $conn->real_escape_string($data['guide_id']);
        $sql = "SELECT status FROM guides WHERE id = '$guide_id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Guide not found']);
            break;
        }
        
        $guide = $result->fetch_assoc();
        if ($guide['status'] !== 'active') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Guide is not active']);
            break;
        }
        
        // Check if itinerary exists
        $itinerary_id = $conn->real_escape_string($data['itinerary_id']);
        $sql = "SELECT id FROM itineraries WHERE id = '$itinerary_id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Itinerary not found']);
            break;
        }
        
        $assignment_date = $conn->real_escape_string($data['assignment_date']);
        $status = $conn->real_escape_string($data['status'] ?? 'assigned');
        $notes = $conn->real_escape_string($data['notes'] ?? '');
        
        $sql = "INSERT INTO guide_assignments (guide_id, itinerary_id, assignment_date, status, notes) 
                VALUES ('$guide_id', '$itinerary_id', '$assignment_date', '$status', '$notes')";
        
        if ($conn->query($sql)) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Assignment created successfully', 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating assignment: ' . $conn->error]);
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
        
        // If guide_id is being updated, check if new guide exists and is active
        if (isset($data['guide_id'])) {
            $guide_id = $conn->real_escape_string($data['guide_id']);
            $sql = "SELECT status FROM guides WHERE id = '$guide_id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Guide not found']);
                break;
            }
            
            $guide = $result->fetch_assoc();
            if ($guide['status'] !== 'active') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Guide is not active']);
                break;
            }
        }
        
        // If itinerary_id is being updated, check if new itinerary exists
        if (isset($data['itinerary_id'])) {
            $itinerary_id = $conn->real_escape_string($data['itinerary_id']);
            $sql = "SELECT id FROM itineraries WHERE id = '$itinerary_id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Itinerary not found']);
                break;
            }
        }
        
        $updates = [];
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $updates[] = "$key = '" . $conn->real_escape_string($value) . "'";
            }
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No fields to update']);
            break;
        }
        
        $sql = "UPDATE guide_assignments SET " . implode(", ", $updates) . " WHERE id = '$id'";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Assignment updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating assignment: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing ID parameter']);
            break;
        }
        
        $id = $conn->real_escape_string($_GET['id']);
        $sql = "DELETE FROM guide_assignments WHERE id = '$id'";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Assignment deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting assignment: ' . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

$conn->close();
?> 