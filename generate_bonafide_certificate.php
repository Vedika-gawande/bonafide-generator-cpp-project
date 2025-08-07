<?php
session_start();
require 'db.php';

if (!isset($_GET['request_id'])) {
    die("Invalid request!");
}

$request_id = $_GET['request_id'];

// Fetch the request details including email and student ID
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die("Request not found!");
}

$recipient_email = $request['email'];
$student_id = $request['id'];
$gender = strtolower($request['gender'] ?? 'not specified');

$pronoun = 'they';
$possessive = 'their';

if ($gender === 'male') {
    $pronoun = 'he';
    $possessive = 'his';
} elseif ($gender === 'female') {
    $pronoun = 'she';
    $possessive = 'her';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonafide Certificate</title>
    <link rel="stylesheet" href="certificate.css">
</head>
<body>
   
    <div class="certificate">
        <div class="header">
            <img src="gpy.jpg" alt="Institute Logo">
            <h2>GOVERNMENT POLYTECHNIC YAVATMAL</h2>
            <p>DHAMANGAON ROAD, YAVATMAL – 445001</p>
            <p>Web Site: www.gpyavatmal.ac.in | E-mail: office.grwpyavatmal@dtemaharashtra.gov.in</p>
            <div class="line"></div>
            <p style="text-align: left;">No. GPY/SS/Bonafide/20<?= date("y"); ?> (ID: <?= $student_id; ?>)</p>
            <p class="date">Date: <?= date("d-m-Y"); ?></p>
        </div>
        <div class="title">BONAFIDE CERTIFICATE</div>
        <div class="content">
            Certified that <strong><?= $request['student_name']; ?></strong> is a bona-fide student of the institute studying in <strong><?= $request['year']; ?></strong> year of the diploma course in <strong><?= $request['branch']; ?></strong> during the year 20<?= date("y", strtotime($request['date'])) - 1; ?> - 20<?= date("y", strtotime($request['date'])); ?>.
            <br><br>
            As per my knowledge, the student bears a good moral character. This certificate is issued for the purpose of <strong><?= $request['reason']; ?></strong> as per <?= $possessive; ?> application date <strong><?= date("d-m-Y", strtotime($request['date'])); ?></strong>.
        </div>
        <div class="signature">
        <img id="signature-image" class="mechanical-signature" src="principal.jpg">
            <p>Principal</p>
            <p>Government Polytechnic,</p>
            <p>Yavatmal<br><br></p>
            <img id="stamp-image" class="stamp" src="stamp1.jpg" style="display: none;">
            <!-- <p id="signed-text" style="display: none;">Signed ✅</p> -->
        </div>
    </div>
</body>
</html>

<!-- Let me know if you want me to refine anything further or adjust the layout! -->


    <div class="button-container">
        <input type="hidden" id="requestId" value="<?= $request_id; ?>">
        <button class="btn" onclick="signCertificate()">Sign</button>
        <button class="btn" onclick="sendBonafideCertificate()">Send</button>
       <button class="btn" onclick="saveAsPDF(<?= $request_id; ?>)">Save as PDF</button>
        <button class="btn" onclick="window.print()">Print</button>
    </div>

    <script>
    function signCertificate() {
        let signedText = document.getElementById("signature-image");
        let stampImage = document.getElementById("stamp-image");

        if (!stampImage) {
            alert("Stamp image not found!");
            return;
        }

        stampImage.style.display = "block";
        signedText.style.display = "block";

        alert("Certificate Signed Successfully!");
    }

    function sendBonafideCertificate() {
        var recipientEmail = "<?= htmlspecialchars($recipient_email, ENT_QUOTES, 'UTF-8'); ?>";
        var studentId = "<?= htmlspecialchars($student_id, ENT_QUOTES, 'UTF-8'); ?>";
        var studentName = "<?= htmlspecialchars($request['student_name'], ENT_QUOTES, 'UTF-8'); ?>";
        var requestId = document.getElementById('requestId').value;

        if (!recipientEmail || !requestId) {
            alert("Recipient email not found or request ID missing.");
            return;
        }

        fetch('generate_pdf.php?request_id=' + requestId)
            .then(response => response.json())
            .then(data => {
                if (data.fileUrl) {
                    var subject = "Your Bonafide Certificate for " + studentName + " (ID: " + studentId + ")";
                    var pdfLink = data.fileUrl;
                    var body = `Dear ${studentName},\n\nYour bonafide certificate has been generated. You can download it using the following link:\n\n${pdfLink}\n\nRegards,\nGovernment Polytechnic Yavatmal`;
                    
                    window.location.href = `mailto:${recipientEmail}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
                } else {
                    alert("Failed to generate the certificate! " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Failed to send the certificate!");
            });
    }

    function saveAsPDF(requestId) {
    fetch('generate_pdf.php?request_id=' + requestId)
        .then(response => response.json())
        .then(data => {
            // Display the message returned from the server
            alert(data.message);
            
            // Open the generated PDF in a new tab or window
            window.open(data.fileUrl, '_blank');
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to generate PDF!");
        });
}
    </script>
</body>
</html>