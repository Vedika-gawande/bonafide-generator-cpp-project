<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $_SESSION['email'] = $email;

    // try {
    //     $stmt = $pdo->prepare("INSERT INTO students (email) VALUES (?) ON DUPLICATE KEY UPDATE email = ?");
    //     $stmt->execute([$email, $email]);
    // } catch (PDOException $e) {
    //     echo "Error: " . $e->getMessage();
    // }
}
?>
