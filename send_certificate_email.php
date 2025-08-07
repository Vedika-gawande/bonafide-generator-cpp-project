<?php
session_start();
require 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $recipientEmail = $_POST['email'];

    if (!$request_id || !$recipientEmail) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request ID or email!']);
        exit;
    }

    // Define server path and PDF link
    $domain = '192.168.211.136'; // Your server IP or domain
    $pdfPath = "cppfinal/bonafide_certificates/Bonafide_Certificate_$request_id.pdf";
    $pdfLink = "http://$domain/$pdfPath";

    // Check if the PDF file exists
    if (!file_exists("C:/xampp/htdocs/$pdfPath")) {
        echo json_encode(['status' => 'error', 'message' => 'Certificate file not found!']);
        exit;
    }

    // Email content
    $subject = "Your Bonafide Certificate";
    $message = "
    <html>
    <body>
        <p>Dear Student,</p>
        <p>Your bonafide certificate is ready. Click the link below to download it:</p>
        <p><a href='$pdfLink' style='color: #007bff; text-decoration: underline;' target='_blank'>Download Bonafide Certificate</a></p>
        <p>If the link above doesn't work, you can copy and paste the following URL into your browser:</p>
        <p><a href='$pdfLink'>$pdfLink</a></p>
        <p>Best Regards,<br>Government Polytechnic Yavatmal</p>
    </body>
    </html>
    ";

    // Email headers
    $headers = "From: office.grwpyavatmal@dtemaharashtra.gov.in\r\n";
    $headers .= "Reply-To: office.grwpyavatmal@dtemaharashtra.gov.in\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    if (mail($recipientEmail, $subject, $message, $headers)) {
        echo json_encode(['status' => 'success', 'message' => "Certificate link sent successfully to $recipientEmail!"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
    }
}
?>
