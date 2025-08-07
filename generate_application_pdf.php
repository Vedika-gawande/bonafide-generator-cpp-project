<?php
session_start();
require 'db.php';
require_once('tcpdf/tcpdf.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['request_id'])) {
    die("Invalid request!");
}

$request_id = $_POST['request_id'];

// Fetch request details from database
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die("Request not found!");
}

// Extract student details
$student_name = htmlspecialchars($request['student_name']);
$branch = htmlspecialchars($request['branch']);
$year = htmlspecialchars($request['year']);
$reason = !empty($request['reason']) ? htmlspecialchars($request['reason']) : 'Not Specified';
$date = date("d-m-Y", strtotime($request['date']));

// Ensure directory exists for PDF storage
if (!file_exists('applications')) {
    mkdir('applications', 0777, true);
}

// Create PDF Object
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Bonafide Certificate Application');
$pdf->SetMargins(15, 10, 15);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Application Content
$html = '
<h3 align="center">Bonafide Certificate Application</h3>
<p>To,<br>The HOD,<br>Government Polytechnic, Yavatmal<br>445001</p>
<p><strong>Subject:</strong> Request for Bonafide Certificate</p>
<p>Respected Sir/Madam,</p>
<p>I, <strong>' . $student_name . '</strong>, a student of <strong>' . $year . ' Year</strong> in the <strong>' . $branch . '</strong> branch, with enrollment number <strong>' . $request['enrollment_number'] . '</strong>, kindly request a bonafide certificate for the purpose of <strong>' . $reason . '</strong>.</p>
<p>My details are as follows:</p>
<ul>
    <li><strong>Phone:</strong> ' . $request['phone'] . '</li>
    <li><strong>Email:</strong> ' . $request['email'] . '</li>
    <li><strong>Location:</strong> ' . $request['location'] . '</li>
    <li><strong>Date of Request:</strong> ' . $date . '</li>
</ul>
<p>I would be grateful if you could process my request at your earliest convenience. Thank you for your consideration.</p>
<p>Yours sincerely,<br><strong>' . $student_name . '</strong></p>
';

// Add content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Save PDF file and trigger download
$pdfFilePath = __DIR__ . "/applications/application_request_$request_id.pdf";
$pdf->Output($pdfFilePath, 'F'); // Save to server
$pdf->Output("application_request_$request_id.pdf", 'D'); // Trigger download

exit();
?>
