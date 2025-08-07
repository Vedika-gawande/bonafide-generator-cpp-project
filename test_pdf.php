<?php
require('fpdf186/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Hello, World!');
$pdfDir = __DIR__ . "/bonafide_certificates";
if (!is_dir($pdfDir)) {
    mkdir($pdfDir, 0777, true);
}

$filePath = $pdfDir . "/test.pdf";
$pdf->Output($filePath, 'F');

if (file_exists($filePath)) {
    echo "✅ PDF generated: <a href='bonafide_certificates/test.pdf'>Open PDF</a>";
} else {
    echo "❌ PDF NOT created!";
}
?>
