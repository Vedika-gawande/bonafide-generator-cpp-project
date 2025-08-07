<?php
session_start();
require 'db.php';
require_once('tcpdf/tcpdf.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['request_id'])) {
    die("Invalid request!");
}

$request_id = $_POST['request_id'];

// Fetch request details from database
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die("Request not found!");
}

// Extract student details
$student_name = htmlspecialchars($request['student_name']);
$branch = htmlspecialchars($request['branch']);
$year = htmlspecialchars($request['year']);
$reason = !empty($request['reason']) ? htmlspecialchars($request['reason']) : 'Not Specified';
$date = date("d-m-Y", strtotime($request['date']));

// Ensure directory exists for PDF storage
if (!file_exists('view_application')) {
    mkdir('view_application', 0777, true);
}

// Create PDF Object
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Bonafide Certificate Application');
$pdf->SetMargins(15, 10, 15);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Application Content
$html = '
<h3 align="center">Bonafide Certificate Application</h3>
<p>To,<br>
The HOD,<br>
Government Polytechnic, Yavatmal<br>
445001</p>
<p><strong>Subject:</strong> Request for Bonafide Certificate</p>
<p>Respected Sir/Madam,</p>
<p>I <strong>' . $student_name . '</strong>, a student of <strong>' . $year . ' Year</strong> in the <strong>' . $branch . '</strong> branch, with enrollment number <strong>' . $request['enrollment_number'] . '</strong>, kindly request a bonafide certificate for the purpose of <strong>' . $reason . '</strong>.</p>
<p>My details are as follows:</p>
<ul>
    
    <li><strong>Email:</strong> ' . $request['email'] . '</li>
    
    <li><strong>Date of Request:</strong> ' . $date . '</li>
</ul>
<p>I would be grateful if you could process my request at your earliest convenience. Thank you for your consideration.</p>
<p>Yours sincerely,<br>
<strong>' . $student_name . '</strong></p>
';

// Add content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Save PDF file
$pdfFilePath = __DIR__ . "/view_application/application_request_$request_id.pdf";
$pdf->Output($pdfFilePath, 'F');

// Generate download link
// $hostUrl = 'http://localhost/cppfinal'; 
// $pdfUrl = $hostUrl . '/bonafide_certificates/application_request_' . $request_id . '.pdf';

// // Email link using mailto
// if (file_exists($pdfFilePath)) {
//     echo '<a href="mailto:' . htmlspecialchars($request['email']) . '?subject=Bonafide Certificate Application Submitted&body=Hello%20' . urlencode($student_name) . ',%0A%0AYour%20bonafide%20certificate%20application%20has%20been%20submitted%20successfully.%0A%0AYou%20can%20download%20your%20application%20PDF%20using%20the%20link%20below:%0A%0A' . urlencode($pdfUrl) . '%0A%0AThank%20you!%0A%0ARegards,%0AGovernment%20Polytechnic%20Yavatmal">Send Application Link via Email</a>';
// } else {
//     echo "Failed to generate application PDF.";
// }
?>
     
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonafide Certificate Application</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="a4-document" id="content">
        <br>
        <h1 class="title">Bonafide Certificate Application</h1>
      
        <div class="details">
            <br>
            <p>To,</p>
            <p>The HOD,</p>
            <p>Government Polytechnic,</p>
            <p>Yavatmal, 445001</p>
            <br>
            <p class="subject">Subject: Request for Bonafide Certificate</p>
            <p>Respected Sir/Madam,</p>
            <p>I, <strong><?= htmlspecialchars($request['student_name']); ?></strong>, a student of <strong><?= htmlspecialchars($request['year']); ?></strong> of <strong><?= htmlspecialchars($request['branch']); ?></strong>. 
            My enrollment number is <strong><?= htmlspecialchars($request['enrollment_number']); ?></strong>. 
            I kindly request a bonafide certificate as I need it for <strong><?= htmlspecialchars($request['reason']); ?></strong>.</p>
            <br>
            <p>My details are as follows:</p>
            <!--<p>Phone: <strong><?= htmlspecialchars($request['phone']); ?></strong></p>-->
            <p>Email: <strong><?= htmlspecialchars($request['email']); ?></strong></p>
            <!--<p>Location: <strong><?= htmlspecialchars($request['location']); ?></strong></p>-->
            <p>Date: <strong><?= htmlspecialchars($request['date']); ?></strong></p>
            <br>
            <p>I would be grateful if you could provide me with the required certificate at the earliest. Thank you for your time and consideration.</p>
            <br>
            <p>Yours sincerely,</p>
            <p><strong><?= htmlspecialchars($request['student_name']); ?></strong></p>
            <body>
    
    </style>
</style>
        </div>
    </div>
    <div class="button-container">
        <form id="submit-form" action="<?= $dashboard_url; ?>" method="POST">
            <input type="hidden" name="request_id" value="<?= $request_id; ?>">
            <input type="hidden" name="status" value="Approved">
        </form>
        <button class="sign-label" onclick="pasteImage()">Sign Here</button>
        <button onclick="window.print()">Print</button>
        <button onclick="saveAsPDF()">Save as PDF</button>
        <button onclick="approveApplication('<?= $request['email']; ?>', 'Approved', '<?= $request['id']; ?>')" class="approve-btn" disabled>Approve</button>
</div>
<img id="hodImage" src="electronics.jpg" style="display: none; position: fixed; top: 90%; left: 70%; transform: translate(-200%, -200%); max-width: 10%; max-height: 10%;" />
<script>
  function pasteImage() {
    const img = document.getElementById('hodImage');
    const approveBtn = document.querySelector('.approve-btn');

    img.style.display = 'block';
    approveBtn.disabled = false; // Enable the approve button
    approveBtn.classList.remove('disabled'); // Remove the disabled class for styling
}

function approveApplication(email, status, rowId) {
    const img = document.getElementById('hodImage');

    if (img.style.display === 'block') {
        if (confirm("Are you sure you want to approve this request?")) {
            fetch('update_status1.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `request_id=${rowId}&status=${status}`
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    sendNotification(email, status, rowId);

                    // Visually remove the row from the table
                    const row = document.getElementById('row-' + rowId);
                    if (row) {
                        row.remove(); // Use remove() to fully delete the row
                    }

                    // Optional: Show a success popup
                    alert("Request approved successfully!");
                } else {
                    alert('Failed to update status in the database!');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                alert('An error occurred while updating the status.');
            });
        }
    } else {
        alert('Please sign the application before approving!');
    }
}




</script>
    <div class="popup" id="popup">Request Submitted Successfully!</div>
    <script>
        document.getElementById('submit-form').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('popup').style.display = 'block';
            document.getElementById('content').classList.add('blurred');

            setTimeout(() => {
                document.getElementById('popup').style.display = 'none';
                document.getElementById('content').classList.remove('blurred');
                window.location.href = 'copy.php';
            }, 2000);
        });
        function sendNotification(email,  status, rowId) {
            let subject = status === 'Approved' ? 'Bonafide Request Approved' : 'Bonafide Request Rejected';
            let body = `Your Bonafide request has been ${status} by HOD `;
            let message = `Your Bonafide request has been ${status} by HOD`;

            // Send email
            let mailtoLink = `mailto:${email}?subject=${subject}&body=${body}`;
            window.location.href = mailtoLink;

            // Send message
            sendMessage( message);

            // Hide the row
            document.getElementById('row-' + rowId).style.display = 'none';

            // Update status in database
            updateRequestStatus(rowId, status);
        }

    
    </script>
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: auto;
        background-color: #f9f9f9;
        margin: 20px;
        font-family: 'Times New Roman', Times, serif;
        flex-direction: column;
        transition: filter 0.3s ease;
        
    }
    

        
    .a4-document {
        width: 210mm;
        min-height: 297mm;
        background-color: white;
        padding: 40px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid #ccc;
        text-align: justify;
        transition: filter 0.3s ease;
        
    }
    
    .title {
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .details p {
        margin: 10px 0;
        font-size: 18px;
        line-height: 1.8;
    }

    .subject {
        font-weight: bold;
        text-decoration: underline;
        font-size: 20px;
    }

    .button-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
        
    }

    button {
    display: inline-block;
    width: 150px;
    height: 50px;
    border-radius: 10px;
    border: 1px solid #03045e;
    position: relative;
    overflow: hidden;
    transition: all 0.5s ease-in;
    z-index: 1;
}

button::before,
button::after {
    content: '';
    position: absolute;
    top: 0;
    width: 0;
    height: 100%;
    transform: skew(15deg);
    transition: all 0.5s;
    overflow: hidden;
    z-index: -1;
}

button::before {
    left: -10px;
    background: #240046;
}

button::after {
    right: -10px;
    background: #5a189a;
}

button:hover::before,
button:hover::after {
    width: 58%;
}

button:hover span {
    color: #e0aaff;
    transition: 0.3s;
}

button span {
    color: #03045e;
    font-size: 18px;
    transition: all 0.3s ease-in;
}
.approve-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
    opacity: 0.6;
}


    .popup {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #27ae60;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        display: none;
    }

    .blurred {
        filter: blur(5px);
    }

    @media (max-width: 768px) {
    .a4-document {
        width: 100%;
    }
    
}

@media print {
    body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }
    @page {
        size: A4;
        margin: 0; /* Page ke external margins hata diye */
    }
    .button-container, .popup {
        display: none; /* Buttons aur popups hide kar diye */
    }
    .content {
        width: 100vw; /* Full viewport width */
        height: 100%;
        padding-left: 0; /* Left padding hata diya */
        padding-right: 0; /* Right padding hata diya */
 
    }
}

</style>
</body>
</html>
