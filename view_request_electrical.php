<?php
session_start();
require 'db.php';

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Fetch the request details from the database
    $stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
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
            <p>To,</p>
            <p>The HOD,</p>
            <p>Government Polytechnic,</p>
            <p>Yavatmal, 445001</p>
            <br>
            <p class="subject">Subject: Request for Bonafide Certificate</p>
            <p>Respected Sir/Madam,</p>
            <p>I <strong><?= htmlspecialchars($request['student_name']); ?></strong>, a student of <strong><?= htmlspecialchars($request['year']); ?></strong> of <strong><?= htmlspecialchars($request['branch']); ?></strong>. 
            My enrollment number is <strong><?= htmlspecialchars($request['enrollment_number']); ?></strong>. 
            I kindly request a bonafide certificate as I need it for <strong><?= htmlspecialchars($request['reason']); ?></strong>.</p>
            <br>
            <p>My details are as follows:</p>
           <!-- <p>Phone: <strong><?= htmlspecialchars($request['phone']); ?></strong></p>-->
            <p>Email: <strong><?= htmlspecialchars($request['email']); ?></strong></p>
            <!--<p>Location: <strong><?= htmlspecialchars($request['location']); ?></strong></p>-->
            <p>Date: <strong><?= htmlspecialchars($request['date']); ?></strong></p>
            <br>
            <p>I would be grateful if you could provide me with the required certificate at the earliest. Thank you for your time and consideration.</p>
            <br>
            <p>Yours sincerely,</p>
            <p><strong><?= htmlspecialchars($request['student_name']); ?></strong></p>
            <br>

            <!-- HOD Approval Section -->
            <?php if ($request['status'] === 'Approved' && !empty($request['approved_by'])): ?>
                <hr>
                <h2>Approval Details</h2>
                <p><strong>Status:</strong> <span style='color: green;'>Approved</span></p>
                <p><strong>Approved By:</strong> H.P.Khamkar</p>
                <p><strong>Approval Date:</strong> <?= date("d-m-Y H:i:s", strtotime($request['approved_at'])); ?></p>
                <br>
                <p><strong>HOD Signature:</strong></p>
                <img src="electrical.jpg" alt="HOD Signature" style="width: 200px; height: auto;">
            <?php else: ?>
                <hr>
                <h2>Approval Details</h2>
                <p><strong>Status:</strong> <span style='color: red;'>Pending Approval</span></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="button-container">
        <button onclick="window.print()">Print</button>
        <button onclick="saveAsPDF()">Save as PDF</button>
    </div>

    <script>
    function viewRequest(requestId) {
        window.location.href = 'view_request_computer.php?request_id=' + requestId;
    }

    const { jsPDF } = window.jspdf;

    function saveAsPDF() {
        const pdf = new jsPDF();
        const content = document.getElementById('content');
        
        pdf.html(content, {
            callback: function (pdf) {
                pdf.save('Bonafide_Certificate_Application.pdf');
            }
        });
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
        -webkit-print-color-adjust: exact; /* Ensure colors print accurately */
    }

    @page {
        size: A4;
        margin: 10mm;
    }

    .a4-document {
        width: 100%;
        height: auto;
        padding: 20px;
        box-shadow: none;
        border: none;
        transform: scale(0.95); /* Shrink content slightly to fit page */
    }

    .title {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .details p {
        font-size: 16px;
        line-height: 1.5;
        margin: 5px 0;
    }

    .button-container {
        display: none;
    }

    .approval-details {
        page-break-inside: avoid;
    }
}



</style>

</body>
</html>
<?php
    } else {
        echo "<p>Request not found.</p>";
    }
} else {
    echo "<p>No request ID provided.</p>";
}
?>
