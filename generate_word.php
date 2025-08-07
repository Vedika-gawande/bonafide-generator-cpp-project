<?php
session_start();
require 'db.php';
require_once 'tcpdf/tcpdf.php';
require_once 'vendor/autoload.php'; // This is required if you're using Composer

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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

// Directory for saving documents
$docDir = __DIR__ . '/bonafide_certificates';
if (!is_dir($docDir)) {
    mkdir($docDir, 0777, true);
}

// Generate dynamic file name
$fileName = "Bonafide_Certificate_" . $request['enrollment_number'] . ".docx";
$filePath = $docDir . '/' . $fileName;

// Create a new PHPWord object
$phpWord = new PhpWord();

// Add a new section to the Word document
$section = $phpWord->addSection();

// Add header content (with images)
$logoPath = __DIR__ . '/cppfinal/bonafide_certificates/gpy.jpg';
$signPath = __DIR__ . '/cppfinal/mechanical.jpg';
$stampPath = __DIR__ . '/cppfinal/stamp1.jpg';

// Add logo image
if (file_exists($logoPath)) {
    $section->addImage($logoPath, ['width' => 100, 'height' => 80, 'align' => 'left']);
}

// Add header text
$section->addText("GOVERNMENT POLYTECHNIC YAVATMAL", ['bold' => true, 'size' => 16], ['align' => 'center']);
$section->addText("DHAMANGAON ROAD, YAVATMAL – 445001", ['size' => 12], ['align' => 'center']);
$section->addText("Website: www.gpyavatmal.ac.in | Email: office.grwpyavatmal@dtemaharashtra.gov.in", ['size' => 12], ['align' => 'center']);
$section->addTextBreak(2); // Adds a line break

// Add certificate title
$section->addText("BONAFIDE CERTIFICATE", ['bold' => true, 'size' => 14], ['align' => 'center']);
$section->addTextBreak(1);

// Add certificate content
$section->addText("Certified that " . $request['student_name'] . " is/was a bona-fide student of this institute, studying in the " . $request['year'] . " year of the diploma course in " . $request['branch'] . " during the academic year 20" . (date("y", strtotime($request['date'])) - 1) . " - 20" . date("y", strtotime($request['date'])) . ".", ['size' => 12], ['align' => 'left']);
$section->addText("As per our records, the student bears a good moral character. This certificate is issued for the purpose of " . $request['reason'] . " as per his/her application dated " . date("d-m-Y", strtotime($request['date'])) . ".", ['size' => 12], ['align' => 'left']);
$section->addTextBreak(1);

// Add footer with signature and stamp images
$section->addText("Principal", ['bold' => true, 'size' => 12], ['align' => 'center']);
$section->addText("Government Polytechnic, Yavatmal", ['size' => 12], ['align' => 'center']);
$section->addTextBreak(1);

// Add signature image
if (file_exists($signPath)) {
    $section->addImage($signPath, ['width' => 150, 'height' => 100, 'align' => 'center']);
}

// Add stamp image
if (file_exists($stampPath)) {
    $section->addImage($stampPath, ['width' => 80, 'height' => 60, 'align' => 'center']);
}

// Save the Word document
$phpWord->save($filePath, 'Word2007');

// Construct the file URL dynamically
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$fileUrl = $baseUrl . "/bonafide_certificates/" . urlencode($fileName);

// Insert into database (optional)
$insertStmt = $pdo->prepare("INSERT INTO bonafide_certificates (request_id, file_name, file_url) VALUES (?, ?, ?)");
$insertStmt->execute([$request_id, $fileName, $fileUrl]);

// Return JSON response with the file URL
echo json_encode([
    "message" => "Word document generated successfully!",
    "file" => "bonafide_certificates/$fileName",
    "fileUrl" => $fileUrl
]);

exit();
?>
