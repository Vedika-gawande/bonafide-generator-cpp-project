<?php
session_start();
require 'db.php';
require('fpdf186/fpdf.php'); // Include the FPDF library

if (!isset($_GET['request_id'])) {
    die(json_encode(["message" => "Invalid request!"]));
}

$request_id = $_GET['request_id'];

// Fetch request details
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die(json_encode(["message" => "Request not found!"]));
}

// Directory for saving PDFs
$pdfDir = $_SERVER['DOCUMENT_ROOT'] . '/bonafide_certificates';
if (!is_dir($pdfDir)) {
    mkdir($pdfDir, 0777, true);
}

// Generate dynamic file name
$fileName = "Bonafide_Certificate_" . $request['enrollment_number'] . ".pdf";
$filePath = $pdfDir . '/' . $fileName;

// Create a new PDF document
$pdf = new FPDF('L', 'mm', 'A3');
$pdf->SetCreator('Government Polytechnic Yavatmal');
$pdf->SetAuthor('Government Polytechnic Yavatmal');
$pdf->SetTitle('Bonafide Certificate');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

// Add border around the certificate
$pdf->Rect(15, 15, 400, 270);

// Set font for document
$pdf->SetFont('Times', '', 14);
$pdf->SetY(30);

// Paths to images
$logoPath = "C:/xampp/htdocs/cppfinal/gpy.jpg";
$signPath = "C:/xampp/htdocs/cppfinal/mechanical.jpg";
$stampPath = "C:/xampp/htdocs/cppfinal/stamp1.jpg";


// Check if images exist
if (!file_exists($logoPath)) {
    die("Error: gpy.jpg not found!");
}
if (!file_exists($signPath)) {
    die("Error: mechanical.jpg not found!");
}
if (!file_exists($stampPath)) {
    die("Error: stamp1.jpg not found!");
}


// Add images
// Place the logo slightly to the left
$pdf->Image($logoPath, 25, 20, 60, 40); // X = 25, Y = 20, Width = 60, Height = 35

// Move text closer to the logo
$pdf->SetXY(140, 30); // X = 100, Y = 28 to align better
$pdf->SetFont('Times', 'B', 20);
$pdf->Cell(0, 10, 'GOVERNMENT POLYTECHNIC YAVATMAL', 0, 1, 'L'); // Left-align text
$pdf->Ln(3); // Reduce space
$pdf->SetFont('Times', '', 16);
$pdf->SetX(160);
$pdf->Cell(0, 11, 'DHAMANGAON ROAD, YAVATMAL 445001', 0, 1, 'L'); // Left-aligned
$pdf->SetX(120);
$pdf->Cell(0, 8, 'Website: www.gpyavatmal.ac.in | Email: office.grwpyavatmal@dtemaharashtra.gov.in', 0, 1, 'L');

// Underline
$y = $pdf->GetY();
$pdf->Line(30, $y, 400, $y);
$pdf->Ln(10);

// Request number and date
$pdf->SetFont('Times', 'B', 14);
$pdf->SetX(30);
$pdf->Cell(200, 10, 'No. GPY/SS/Bonafide/20' . date('y') . ' (ID: ' . $request['id'] . ')', 0, 0, 'L');
$pdf->Cell(170, 10, 'Date: ' . date('d-m-Y'), 0, 1, 'R');
$pdf->Ln(5);

// Certificate title
$pdf->SetFont('Times', 'B', 24);
$pdf->Cell(0, 20, 'BONAFIDE CERTIFICATE', 0, 1, 'C');
$y = $pdf->GetY() - 5;
$pdf->Line(155, $y, 265, $y);
$pdf->Ln(10);

// Certificate content
$pdf->SetFont('Times', '', 18);
$pdf->SetX(30);
$pdf->MultiCell(350, 12, 
    "Certified that " . $request['student_name'] . " is a bona-fide student of this institute studying in " . 
    $request['year'] . " year of the diploma course in " . $request['branch'] . 
    " during the year 20" . (date("y", strtotime($request['date'])) - 1) . 
    " - 20" . date("y", strtotime($request['date'])) . ".", 
    0, 'J');

$pdf->Ln(5);

$pdf->SetX(30);
$pdf->MultiCell(350, 12, 
    "As per our records, the student bears a good moral character. This certificate is issued for the purpose of " . 
    $request['reason'] . " as per his/her application dated " . 
    date("d-m-Y", strtotime($request['date'])) . ".", 
    0, 'J');

$pdf->Ln(20);

// Signature & Stamp
$pdf->Image($signPath, 320, 180, 50);
$pdf->Image($stampPath, 250, 180, 50);
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 16);
$pdf->Cell(340, 12, 'Principal', 0, 1, 'R');
$pdf->SetX(50);
$pdf->Cell(180, 12, '', 0, 0, 'L');
$pdf->Cell(150, 12, 'Government Polytechnic', 0, 1, 'R');
$pdf->Cell(340, 12, 'Yavatmal', 0, 1, 'R');

$pdf->Ln(10);

// Prevent Output Buffer Issues
if (ob_get_length()) ob_end_clean();

// Save PDF
$pdf->Output($filePath, 'F');
$serverIp = "192.168.28.136";
$baseUrl = "http://" . $serverIp;
$fileUrl = $baseUrl . "/bonafide_certificates/" . urlencode($fileName);

// Ensure correct permissions for public access
chmod($filePath, 0644);

// Save in Database
$insertStmt = $pdo->prepare("INSERT INTO bonafide_certificates (request_id, file_name, file_url) VALUES (?, ?, ?)");
$insertStmt->execute([$request_id, $fileName, $fileUrl]);

// Send a response with the public URL
echo json_encode([
    "message" => "PDF generated successfully!",
    "fileUrl" => $fileUrl
]);

exit();

?>
