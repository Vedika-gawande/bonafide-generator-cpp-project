<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Load dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

// Validate request
if (!isset($_POST['request_id'])) {
    die("Invalid request!");
}

$request_id = $_POST['request_id'];

// Fetch request details
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die("Request not found!");
}

// Student details
$student_name = $request['student_name'];
$student_email = $request['email'];
$branch = $request['branch'];
$year = $request['year'];
$purpose = isset($request['purpose']) ? $request['purpose'] : 'Not Specified';
$date = date("d-m-Y", strtotime($request['date']));

// Generate PDF using dompdf
$options = new Options();
$options->set('defaultFont', 'Times New Roman');
$dompdf = new Dompdf($options);

$html = "
<html>
<head>
    <style>
        body { font-family: 'Times New Roman', Times, serif; text-align: center; margin: 40px; }
        .certificate { width: 800px; margin: auto; border: 2px solid black; padding: 40px; box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2); height: 450px; }
        .header { text-align: center; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 20px; }
        .header p { margin: 3px 0; font-size: 14px; }
        .line { border-bottom: 2px solid black; margin: 10px 0; }
        .title { font-size: 22px; font-weight: bold; text-decoration: underline; margin-top: 10px; }
        .content { text-align: left; font-size: 18px; margin-top: 20px; line-height: 1.8; }
        .signature { text-align: center; margin-top: 20px; font-weight: bold; }
        .date { text-align: right; margin-top: -30px; }
    </style>
</head>
<body>
    <div class='certificate'>
        <div class='header'>
            <h2>GOVERNMENT POLYTECHNIC YAVATMAL</h2>
            <p>DHAMANGAON ROAD, YAVATMAL – 445001</p>
            <p>Web Site: www.gpyavatmal.ac.in | E-mail: office.grwpyavatmal@dtemaharashtra.gov.in</p>
            <div class='line'></div>
            <p style='text-align: left;'>No. GPY/SS/Bonafide/20" . date("y") . "</p>
            <p class='date'>Date: " . date("d-m-Y") . "</p>
        </div>
        <div class='title'>BONAFIDE CERTIFICATE</div>
        <div class='content'>
            Certified that <strong>$student_name</strong>, is/was a bona-fide student of the institute studying in <strong>$year</strong> year of the diploma course in <strong>$branch</strong> during the year 20" . (date("y") - 1) . " - 20" . date("y") . ".
            <br>
            As per my knowledge, he/she bears a good moral character. This certificate is issued for the purpose of <strong>$purpose</strong> as per his/her application date <strong>$date</strong>.
        </div>
        <div class='signature'>
            <p>Principal</p>
            <p>Government Polytechnic,</p>
            <p>Yavatmal</p>
            <p>Signed ✅</p>
        </div>
    </div>
</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdfContent = $dompdf->output();
$pdfFilePath = "bonafide_certificates/Bonafide_$request_id.pdf";
file_put_contents($pdfFilePath, $pdfContent);

// Send Email with PHP mail() function
$to = $student_email;
$subject = "Bonafide Certificate - Government Polytechnic Yavatmal";
$boundary = md5(time());
$headers = "From: no-reply@gpyavatmal.ac.in\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

// Email body
$body = "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= "<p>Dear $student_name,</p>";
$body .= "<p>Please find your bonafide certificate attached.</p>";
$body .= "<p>Best Regards,<br>Government Polytechnic Yavatmal</p>\r\n";

// Attachment encoding
$fileName = basename($pdfFilePath);
$fileSize = filesize($pdfFilePath);
$fileContent = chunk_split(base64_encode(file_get_contents($pdfFilePath)));

$body .= "--$boundary\r\n";
$body .= "Content-Type: application/pdf; name=\"$fileName\"\r\n";
$body .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n\r\n";
$body .= "$fileContent\r\n";
$body .= "--$boundary--";

// Send the email
$mailSuccess = mail($to, $subject, $body, $headers);

if ($mailSuccess) {
    echo "Bonafide certificate sent to $student_email successfully.";
} else {
    echo "Failed to send email.";
}
?>
