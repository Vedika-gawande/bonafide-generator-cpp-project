<?php
session_start();
require 'db.php';
require_once 'tcpdf/tcpdf.php'; // Include TCPDF library

if (isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    // Fetch request details from database
    $stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        // Create PDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Government Polytechnic Yavatmal');
        $pdf->SetTitle('Bonafide Certificate Application');
        $pdf->SetMargins(20, 20, 20);
        $pdf->AddPage();

        // PDF Content with proper left alignment
        $html = "
            <h2 style='text-align:center;'>Bonafide Certificate Application</h2>
            
            <p style='text-align:left; margin: 0;'><strong>To,</strong></p>
            <p style='text-align:left; margin: 0;'>The HOD,</p>
            <p style='text-align:left; margin: 0;'>Government Polytechnic,</p>
            <p style='text-align:left; margin: 0;'>Yavatmal, 445001</p>
            <p style='text-align:left; margin: 0;'><strong>Subject:</strong> Request for Bonafide Certificate</p>
            <p style='text-align:left; margin: 0;'>Respected Sir/Madam,</p>
            <p style='text-align:left; margin: 0;'>
            I <strong>{$request['student_name']}</strong>, a student of <strong>{$request['year']}</strong> year in the <strong>{$request['branch']}</strong> branch, with enrollment number <strong>{$request['enrollment_number']}</strong>, kindly request a bonafide certificate as I need it for <strong>{$request['reason']}</strong>.
            </p>
            <p style='text-align:left; margin: 0;'><strong>My details are as follows:</strong></p>
            <p style='text-align:left; margin: 0;'><strong>Phone:</strong> {$request['phone']}</p>
            <p style='text-align:left; margin: 0;'><strong>Email:</strong> {$request['email']}</p>
            <p style='text-align:left; margin: 0;'><strong>Location:</strong> {$request['location']}</p>
            <p style='text-align:left; margin: 0;'><strong>Date of Request:</strong> {$request['date']}</p>
            <p style='text-align:left; margin: 0;'>
            I would be grateful if you could provide me with the required certificate at the earliest. Thank you for your time and consideration.
            </p>
            <p style='text-align:left; margin: 0;'>Yours sincerely,</p>
            <p style='text-align:left; margin: 0;'><strong>{$request['student_name']}</strong></p>
        ";

        // Write content to PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Ensure the directory exists
        $directory = 'principal_side_app/';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Define file path
        $file_name = "application_request_{$request_id}.pdf";
        $file_path = $directory . $file_name;

        // Save the PDF
        $pdf->Output(__DIR__ . '/' . $file_path, 'F'); // Save the file in the correct directory

        // Show success message with download link
        echo "<script>
            alert('PDF generated successfully!');
            window.location.href = 'view_request.php?request_id={$request_id}';
        </script>";
    } else {
        echo "<script>alert('Request not found!');</script>";
    }
} else {
    echo "<script>alert('Invalid request!');</script>";
}
?>
