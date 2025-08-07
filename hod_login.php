<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user is an HOD
    $stmt = $pdo->prepare("SELECT * FROM hods WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $hod = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($hod) {
        $_SESSION['hod_branch'] = $hod['branch'];  // Store the HOD's branch in session
        header("Location: hod_dashboard.php");
    } else {
        echo "Invalid credentials!";
    }
}
?>
