<?php
header("Content-Type: application/json");

// Database connection
$host = 'localhost';
$dbname = 'student_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

// Validate enrollment number
if (!isset($_GET['enrollment']) || trim($_GET['enrollment']) === '') {
    echo json_encode(["error" => "Enrollment number not provided."]);
    exit();
}

$enrollment = trim($_GET['enrollment']);

try {
    $stmt = $pdo->prepare("SELECT name,  department, year, email FROM students WHERE enrollment_number = ?");
    $stmt->execute([$enrollment]);

    if ($stmt->rowCount() > 0) {
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            "full_name" => $student['name'],
            //"phone" => $student['phone'],
            "branch" => $student['department'],
            "year" => $student['year'],
            "email" => $student['email'],
            //"location" => $student['location']
        ]);
    } else {
        echo json_encode(["error" => "No data found for enrollment number: $enrollment"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>
