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
            $sql = "SELECT * FROM guides WHERE id = '$id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Guide not found']);
            }
        } else {
            $sql = "SELECT * FROM guides ORDER BY first_name ASC";
            $result = $conn->query($sql);
            $guides = [];
            while ($row = $result->fetch_assoc()) {
                $guides[] = $row;
            }
            echo json_encode($guides);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['first_name']) || !isset($data['last_name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
            break;
        }
        
        $first_name = $conn->real_escape_string($data['first_name']);
        $last_name = $conn->real_escape_string($data['last_name']);
        $phone_number = $conn->real_escape_string($data['phone_number'] ?? '');
        $email = $conn->real_escape_string($data['email'] ?? '');
        $car_plate_number = $conn->real_escape_string($data['car_plate_number'] ?? '');
        $status = $conn->real_escape_string($data['status'] ?? 'active');
        
        $sql = "INSERT INTO guides (first_name, last_name, phone_number, email, car_plate_number, status) 
                VALUES ('$first_name', '$last_name', '$phone_number', '$email', '$car_plate_number', '$status')";
        
        if ($conn->query($sql)) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Guide created successfully', 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating guide: ' . $conn->error]);
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
        
        $sql = "UPDATE guides SET " . implode(", ", $updates) . " WHERE id = '$id'";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Guide updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating guide: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing ID parameter']);
            break;
        }
        
        $id = $conn->real_escape_string($_GET['id']);
        
        // First check if guide has any assignments
        $sql = "SELECT COUNT(*) as count FROM guide_assignments WHERE guide_id = '$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete guide with existing assignments']);
            break;
        }
        
        $sql = "DELETE FROM guides WHERE id = '$id'";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Guide deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting guide: ' . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

$conn->close();
?> 