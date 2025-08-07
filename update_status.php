<?php
session_start();
require 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Update request status
    $stmt = $pdo->prepare("UPDATE bonafide_requests SET status = ? WHERE id = ?");
    $stmt->execute([$status, $request_id]);

    // Fetch the student's email for notification
    // $stmt = $pdo->prepare("SELECT email, student_name FROM bonafide_requests WHERE id = ?");
    // $stmt->execute([$request_id]);
    // $request = $stmt->fetch(PDO::FETCH_ASSOC); 
}
?>
    