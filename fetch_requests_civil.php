<?php

// fetch_requests.php

header('Content-Type: application/json');
ob_start(); // Start output buffering to prevent stray output

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_database');

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$type = $_GET['type'] ?? '';
$searchQuery = $_GET['search'] ?? '';

$data = [];

// Base query, ensuring only Civil Engineering records are retrieved
$sql = "SELECT id, student_name, enrollment_number, branch, year, status, 
               DATE_FORMAT(date, '%Y-%m-%d') AS date 
        FROM bonafide_requests 
        WHERE branch = 'Civil Engineering'"; // Always filter by branch

// Add conditions based on the request type
switch ($type) {
    case 'approved':
        $sql .= " AND status = 'Approved'";
        break;
    case 'rejected':
        $sql .= " AND status = 'Rejected'";
        break;
    case 'pending':
        $sql .= " AND status = 'Pending'";
        break;
    case 'total':
        // No additional condition needed, as we're already filtering by branch
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid request type']);
        $conn->close();
        exit;
}

// Add search functionality
if (!empty($searchQuery)) {
    $searchQuery = $conn->real_escape_string($searchQuery);
    $sql .= " AND (student_name LIKE '%$searchQuery%' OR enrollment_number LIKE '%$searchQuery%')";
}

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $conn->error]);
    $conn->close();
    exit;
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();
ob_end_clean(); // Clean any previous output

// Return the data as JSON
echo json_encode(['status' => 'success', 'data' => $data]);

?>
