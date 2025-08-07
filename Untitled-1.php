<?php
// Include the TCPDF library
require_once('tcpdf_include.php'); // Ensure TCPDF is included correctly

// Create instance of TCPDF
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Government Polytechnic Yavatmal');
$pdf->SetTitle('Bonafide Certificate');
$pdf->SetSubject('Bonafide Certificate');

// Add a page
$pdf->AddPage();

// Prepare the certificate content with embedded CSS
$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Bonafide Certificate</title>
    <style>
        /* Body styling */
        body {
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
            margin: 40px;
            padding-top: 70px;
        }

        /* Certificate styling */
        .certificate {
            width: 800px;
            margin: auto;
            border: 2px solid black;
            padding: 40px;
            position: relative;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            height: 450px;
        }

        /* Header image styling */
        .header img {
            width: 100px;
            position: absolute;
            left: 50px;
            top: 20px;
            height: 80px;
        }

        /* Header text */
        .header {
            text-align: center;
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

        /* Line separator */
        .line {
            border-bottom: 2px solid black;
            margin: 10px 0;
        }

        /* Title text styling */
        .title {
            font-size: 22px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }

        /* Content styling */
        .content {
            text-align: left;
            font-size: 14px;
            margin-top: 20px;
            line-height: 1.8;
        }

        /* Signature and Date styling */
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

        /* Button container styling */
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

        /* Hide signed text */
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
        
        /* Print page size and margin */
        @media print {
            @page {
                size: A4 landscape;
                margin: 20mm;
            }

            body {
                transform: rotate(0deg); /* Prevent accidental rotation */
                margin: 0;
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
            <span>No. GPY/SS/Bonafide/20" . date('y') . " (ID: " . $request['id'] . ")</span>
            <span style='text-align: right;'>Date: " . date('d-m-Y') . "</span>
        </div>

        <h3 style='text-align: center; text-decoration: underline;'>BONAFIDE CERTIFICATE</h3>

        <p style='text-align: justify; font-size: 14px;'>
            Certified that <strong>{$request['student_name']}</strong>, is/was a bona-fide student of this institute studying in <strong>{$request['year']}</strong> year of the diploma course in <strong>{$request['branch']}</strong> during the year 20" . (date("y", strtotime($request['date'])) - 1) . " - 20" . date("y", strtotime($request['date'])) . ".
        </p>

        <p style='text-align: justify; font-size: 14px;'>
            As per our records, the student bears a good moral character. This certificate is issued for the purpose of <strong>{$request['reason']}</strong> as per his/her application dated <strong>" . date("d-m-Y", strtotime($request['date'])) . "</strong>.
        </p>

        <div style='margin-top: 50px; text-align: right;'>
            <p><strong>Principal</strong></p>
            <p>Government Polytechnic, Yavatmal</p>
            $signHtml
            <br>
            $stampHtml
        </div>
    </div>
</body>
</html>
";

// Write the HTML to TCPDF and generate PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output the generated PDF (force download)
$pdf->Output('bonafide_certificate_' . $request['id'] . '.pdf', 'D');
?>
