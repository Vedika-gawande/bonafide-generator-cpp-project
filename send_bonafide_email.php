<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["message" => "Invalid request!"]));
}

$email = $_POST['email'] ?? '';
$request_id = $_POST['request_id'] ?? '';

if (empty($email) || empty($request_id)) {
    die(json_encode(["message" => "Email and Request ID are required!"]));
}

// Fetch student details from DB
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die(json_encode(["message" => "Request not found!"]));
}

// PDF file path
$pdfFile = __DIR__ . "/bonafide_certificates/Bonafide_Certificate_" . $request['enrollment_number'] . ".pdf";
if (!file_exists($pdfFile)) {
    die(json_encode(["message" => "PDF file not found!"]));
}

// Read PDF file content
$fileContent = chunk_split(base64_encode(file_get_contents($pdfFile)));
$filename = "Bonafide_Certificate.pdf";

// Email headers
$boundary = md5(time());
$headers = "From: Government Polytechnic Yavatmal <vedikagawande91@gmail.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

// Email body
$body = "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$body .= "Dear {$request['student_name']},<br><br>Your bonafide certificate is attached.<br><br>Best Regards,<br>Government Polytechnic Yavatmal\r\n\r\n";

// Attach PDF
$body .= "--$boundary\r\n";
$body .= "Content-Type: application/pdf; name=\"$filename\"\r\n";
$body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n\r\n";
$body .= $fileContent . "\r\n\r\n";
$body .= "--$boundary--";

// Send Email
if (mail($email, "Bonafide Certificate", $body, $headers)) {
    echo json_encode(["message" => "Email sent successfully to " . $email]);
} else {
    echo json_encode(["message" => "Email failed to send!"]);
}
?>
