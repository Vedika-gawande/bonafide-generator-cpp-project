<?php
require_once('tcpdf/tcpdf.php');

// Create new PDF instance
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Test PDF');
$pdf->SetSubject('Testing TCPDF');

// Add a page
$pdf->AddPage();

// Add content
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, 'Hello, this is a test PDF created using TCPDF!');

// Output PDF to browser
$pdf->Output('test.pdf', 'I');
?>
