<?php
require_once('tcpdf/tcpdf.php');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'college_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dummy student details (replace with actual form data)
$studentID = 'STU001';
$studentName = 'John Doe';
$course = 'Computer Engineering';
$issueDate = date('Y-m-d');

// Generate a unique certificate ID and folder name
$certificateID = uniqid('CERT_');
$folderName = __DIR__ . "/bonafide_certificates/student_ID_" . uniqid();

// Create the directory if it doesn’t exist
if (!file_exists($folderName)) {
    if (!mkdir($folderName, 0777, true)) {
        die("Failed to create directory: $folderName");
    }
}

// Store certificate data in the database
$stmt = $conn->prepare("INSERT INTO certificates (student_id, student_name, course, issue_date, certificate_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $studentID, $studentName, $course, $issueDate, $certificateID);
$stmt->execute();
$stmt->close();
$conn->close();

// Create the verification URL
$verificationURL = "http://localhost/verify_certificate.php?id=$certificateID";

// Generate the PDF with QR code
$pdf = new TCPDF();
$pdf->AddPage();

$pdf->SetFont('helvetica', '', 16);
$pdf->Cell(0, 10, 'Bonafide Certificate', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(0, 10, "This is to certify that $studentName (ID: $studentID) is a bonafide student of the $course course. The certificate was issued on $issueDate.", 0, 'L');
$pdf->Ln(20);

// Add the QR code
$pdf->write2DBarcode($verificationURL, 'QRCODE,H', 150, 240, 50, 50);

// Save the PDF inside the unique folder
$pdfPath = "$folderName/bonafide_$studentID.pdf";

$pdf->Output($pdfPath, 'F');

if (file_exists($pdfPath)) {
    echo "Certificate generated successfully! <a href='" . str_replace(__DIR__, '', $pdfPath) . "'>Download Certificate</a>";
} else {
    echo "Failed to generate certificate.";
}
?>
