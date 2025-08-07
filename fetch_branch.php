<?php
require 'db.php'; // Include your DB connection file

if (isset($_GET['request_id']) && is_numeric($_GET['request_id'])) {
    $request_id = intval($_GET['request_id']);

    global $pdo;

    // Fetch branch from bonafide_requests table
    $stmt = $pdo->prepare("SELECT branch FROM bonafide_requests WHERE id = :request_id");
    $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $branch = strtolower(trim($request['branch']));

        // Redirect to the corresponding dashboard with request_id
        switch ($branch) {
            case "computer engineering":
                header("Location: view_request_computer.php?request_id=" . $request_id);
                exit();
            case "electronics engineering":
            case "extc":
            case "electronics and telecommunication engineering":
                header("Location: view_request_electronics.php?request_id=" . $request_id);
                exit();
            case "electrical engineering":
                header("Location: view_request_electrical.php?request_id=" . $request_id);
                exit();
            case "civil engineering":
                header("Location: view_request.php?request_id=" . $request_id);
                exit();
            case "mechanical engineering":
                header("Location: view_request_mechanical.php?request_id=" . $request_id);
                exit();
            default:
                echo "<script>alert('Dashboard not found for branch: " . htmlspecialchars($branch) . "'); window.history.back();</script>";
                break;
        }
    } else {
        echo "<script>alert('Bonafide request not found with ID: " . htmlspecialchars($request_id) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request ID.'); window.history.back();</script>";
}
?>
