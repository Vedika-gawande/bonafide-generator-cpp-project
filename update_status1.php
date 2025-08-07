<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Received Data: " . print_r($_POST, true));

    if (empty($_POST['request_id']) || empty($_POST['status'])) {
        error_log("❌ Invalid or missing request_id/status!");
        die("error: request_id or status missing");
    }

    $request_id = intval($_POST['request_id']); // Ensure it's an integer
    $status = $_POST['status'];
    $approved_at = date("Y-m-d H:i:s");

    // Fetch student branch
    $stmt = $pdo->prepare("SELECT branch FROM bonafide_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        error_log("❌ Request ID $request_id NOT FOUND!");
        die("request_not_found");
    }

    $branch = $request['branch'];

    // Map branches to HOD names
    $hodNames = [
        'Computer Engineering' => 'S.S.Mete',
        'Electronics Engineering' => 'P.P.Pawar',
        'Electrical Engineering' => 'H.P.Khamkar',
        'Mechanical Engineering' => 'A.P.Matale',
        'Civil Engineering' => 'N.Quereshi'
    ];

    $hodName = $hodNames[$branch] ?? 'HOD';

    // Update request status with HOD name
    $stmt = $pdo->prepare("UPDATE bonafide_requests SET status = ?, approved_by = ?, approved_at = ? WHERE id = ?");
    $stmt->execute([$status, $hodName, $approved_at, $request_id]);

    if ($stmt->rowCount()) {
        error_log("✅ Request ID $request_id updated successfully!");
        echo 'success';
    } else {
        error_log("❌ Failed to update Request ID $request_id!");
        echo 'fail';
    }
} else {
    error_log("❌ Invalid request method!");
    echo 'invalid_request';
}
?>
