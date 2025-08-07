<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = $_POST['student_name'];
    $enrollment_number = $_POST['enrollment_number'];
    $reason = $_POST['reason'];
    $teacher_email = "vedikagawande91@gmail.com"; // Change to dynamic teacher email

    // Insert request into the database
    $stmt = $pdo->prepare("INSERT INTO bonafide_requests (student_name, enrollment_number, reason, teacher_email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$student_name, $enrollment_number, $reason, $teacher_email]);

    $requestId = $pdo->lastInsertId(); // Get the last inserted request ID

    // Send email notification (using basic PHP mail function)
    if (mail($teacher_email, "New Bonafide Request", "A new bonafide certificate request has been submitted by $student_name. Request ID: $requestId")) {
        $_SESSION['success_message'] = "Request submitted! A teacher will review it soon.";
    } else {
        $_SESSION['error_message'] = "Request submitted, but failed to send email.";
    }

    // Send push notification (trigger from backend)
    $notificationMessage = json_encode([
        'message' => "Your bonafide request has been approved/rejected! Check your dashboard."
    ]);

    // Send notification to the backend
    file_get_contents("http://localhost:3000/send-notification", false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $notificationMessage
        ]
    ]));

    header('Location: student_dashboard.php');
    exit();
}
?>
