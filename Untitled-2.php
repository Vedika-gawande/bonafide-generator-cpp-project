<?php
/*$cssFile = 'certificate.css';*/
session_start();
require 'db.php';
require_once('tcpdf/tcpdf.php');

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
$pdfDir = __DIR__ . '/bonafide_certificates';
if (!is_dir($pdfDir)) {
    mkdir($pdfDir, 0777, true);
}

// Generate dynamic file name
$fileName = "Bonafide_Certificate_" . $request['enrollment_number'] . ".pdf";
$filePath = $pdfDir . '/' . $fileName;

// Create PDF
$pdf = new TCPDF('L', 'mm', 'A3', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Government Polytechnic Yavatmal');
$pdf->SetTitle('Bonafide Certificate');
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

// Logo and stamp paths
$logoPath = __DIR__ . '/bonafide_certificates/gpy.jpg';
$signPath = __DIR__ . '/bonafide_certificates/mechanical.jpg';
$stampPath = __DIR__ . '/bonafide_certificates/stamp1.jpg';

// Check if files exist
$logoHtml = file_exists($logoPath) ? "<img src='$logoPath' width='80'>" : "<p style='color: red;'>Logo not found</p>";
$signHtml = file_exists($signPath) ? "<img src='$signPath' width='120'>" : "<p style='color: red;'>Signature not found</p>";
$stampHtml = file_exists($stampPath) ? "<img src='$stampPath' width='60'>" : "<p style='color: red;'>Stamp not found</p>";

// Prepare certificate content
$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Bonafide Certificate</title>
    <style>
        /* Certificate Styling */
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            /*text-align: right;*/
           
                }

        .certificate {
    width: 600px;
    margin: auto;
    border: 5px solid black; /* Add a border */
    padding: 40px;
    <!--text-align: left;-->
    position: relative;
    box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
    height: 300px;
    background-color: #fff; /* White background to make the border stand out */
}


        .header img {
            width: 100px;
            position: absolute;
            left: 50px;
            top: 20px;
            height: 80px;
        }

        .header {
            text-align: center;
             margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }

        .header p {
            margin: 3px 0;
            font-size: 14px;
        }

        .line {
            border-bottom: 2px solid black;
            margin: 10px 0;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }

        .content {
            text-align: left;
            font-size: 16px;
            margin-top: 20px;
            line-height: 1.8;
        }

        .signature {
            text-align: center;
            margin-top: -10px;
            font-weight: bold;
            line-height: 0.2;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            margin-left: 550px;
            position: relative;
        }

        .date {
            text-align: right;
            margin-top: -30px;
        }

        .button-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: rgb(123, 61, 217);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(123, 61, 217);
        }

        #signed-text {
            font-size: 16px;
            font-style: italic;
            color: rgb(127, 73, 209);
            display: none;
        }

        @media print {
            .button-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class='certificate'>
        <div class='header'>
            $logoHtml
            <h2 style='margin: 0;'>GOVERNMENT POLYTECHNIC YAVATMAL</h2>
            <p style='margin: 0;'>DHAMANGAON ROAD, YAVATMAL – 445001</p>
            <p style='margin: 0;'>Website: www.gpyavatmal.ac.in | Email: office.grwpyavatmal@dtemaharashtra.gov.in</p>
        </div>
        <hr>
        <div style='display: flex; justify-content: space-between;'>
            <p><span>No. GPY/SS/Bonafide/20" . date('y') . " (ID: " . $request['id'] . ")</span>
            <span style='text-align: left;'>Date: " . date('d-m-Y') . "</span></p>
        </div>
        
        <h3 style='text-align: center; text-decoration: underline;'>BONAFIDE CERTIFICATE</h3>
        <p style='text-align: justify; font-size: 14px;'>
            Certified that <strong>{$request['student_name']}</strong>, is/was a bona-fide student of this institute studying in <strong>
            <p>{$request['year']}</strong> year of the diploma course in <strong>{$request['branch']}</strong>during the year 20" . (date("y", strtotime($request['date'])) - 1) . " - 20" . date("y", strtotime($request['date'])) . ".</p>
        </p>

        <p style='text-align: justify; font-size: 14px;'>
            As per our records, the student bears a good moral character. This certificate is issued for the purpose of <p><strong>{$request['reason']}</strong> as per his/her application dated <strong>" . date("d-m-Y", strtotime($request['date'])) . "</strong>.</p>
        </p>

        <div class='signature'>
            <p><strong>Principal</strong></p>
            <p>Government Polytechnic,</p>
            <p>Yavatmal</p>
            <img src='cppfinal/mechanical.jpg' alt='Mechanical Image' style='width: 150px; height: auto;'>
            <br>
            $stampHtml
        </div>
    </div>
</body>
";

//;Write the HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($filePath, 'F');
//$pdf->Output('bonafide_certificate_' . $request['id'] . '.pdf', 'D');

// Construct the file URL dynamically
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$fileUrl = $baseUrl . "/bonafide_certificates/" . urlencode($fileName);

// Return JSON response
// header('Content-Type: application/json');
$insertStmt = $pdo->prepare("INSERT INTO bonafide_certificates (request_id, file_name, file_url) VALUES (?, ?, ?)");
$insertStmt->execute([$request_id, $fileName, $fileUrl]);

echo json_encode([
    "message" => "PDF generated successfully!",
    "file" => "bonafide_certificates/$fileName",
    "fileUrl" => "http://192.168.211.136/cppfinal/bonafide_certificates/Bonafide_Certificate_$fileName"
]);

exit();
?>
