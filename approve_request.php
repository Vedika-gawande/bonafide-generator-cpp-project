<?php
require 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id']) && isset($_POST['status'])) {
    session_start();

    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $hod_name = $_SESSION['hod_name'] ?? 'Unknown'; // Get HOD name from session

    try {
        // Update request status, approved_by, and timestamp in the database
        $stmt = $pdo->prepare("UPDATE bonafide_requests SET status = ?, approved_by = ?, approved_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $hod_name, $request_id]);

        if ($stmt->rowCount() > 0) {
            // Success message
            $_SESSION['success_message'] = "Request #$request_id successfully updated to '$status' by $hod_name.";
        } else {
            // No rows affected, meaning request ID might not exist or status is unchanged
            $_SESSION['error_message'] = "Failed to update request status. Please try again.";
        }
    } catch (PDOException $e) {
        // Catch database errors
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }

    // Redirect back to the Civil dashboard
    header("Location: civil_dashboard.php");
    exit();
} else {
    // Handle invalid access
    $_SESSION['error_message'] = "Invalid request. Please try again.";
    header("Location: civil_dashboard.php");
    exit();
}
?>
