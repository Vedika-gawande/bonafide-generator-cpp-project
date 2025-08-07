<?php
require 'db.php';

if (isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    global $pdo;

    // Mark the request as signed
    $sql = "UPDATE bonafide_requests SET signed = 1 WHERE id = :request_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
