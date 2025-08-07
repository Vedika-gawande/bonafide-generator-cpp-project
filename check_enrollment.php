<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";  // Change if needed
$password = "";
$database = "student_database";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the enrollment number from request
$enrollment = isset($_GET['enrollment']) ? $_GET['enrollment'] : '';

if (empty($enrollment)) {
    die(json_encode(["success" => false, "message" => "No enrollment number provided."]));
}

// List of tables to check
$tables = ["cei", "ceii", "ceiii", "coi", "coii", "coiii", "eei", "eeii", "eeiii", "eji", "ejii", "ejiii", "logins", "mei", "meii", "meiii", "students"];

$found = false;
foreach ($tables as $table) {
    $sql = "SELECT enrollment_number FROM $table WHERE enrollment_number = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die(json_encode(["success" => false, "message" => "Query preparation failed: " . $conn->error]));
    }

    $stmt->bind_param("s", $enrollment);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $found = true;
        break;  // Stop checking once found
    }
}

$stmt->close();
$conn->close();

if ($found) {
    // Redirect to another page if enrollment number is found
    die("start");
    header("Location:/otpgen.php");
    die("hello");
    exit();
} else {
    echo json_encode(["success" => false, "message" => "Enrollment number not found."]);
}
?>
