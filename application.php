<?php
session_start();
require 'db.php';

// Fetch student details if request_id is passed
if (isset($_GET['request_id']) && !empty($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Validate request_id as an integer
    if (!filter_var($request_id, FILTER_VALIDATE_INT)) {
        die("<p>Invalid request ID.</p>");
    }

    $stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE id = :id");
    $stmt->execute(['id' => $request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        die("<p>Request not found.</p>");
    }
} else {
    die("<p>No request ID provided.</p>");
}

// Determine the submission endpoint based on the department
// Normalize the branch name to avoid inconsistencies
$branch = strtolower(trim($request['branch']));

switch ($branch) {
    case 'computer engineering':
        $dashboard_url = 'teacher_dashboard.php';
        break;
    case 'civil engineering':
        $dashboard_url = 'civil_dashboard.php';
        break;
    case 'electrical engineering':
        $dashboard_url = 'electrical_dashboard.php';
        break;
    case 'electronics engineering':
    case 'extc':
    case 'electronics and telecommunication engineering':
        $dashboard_url = 'electronics_dashboard.php';
        break;
    case 'mechanical engineering':
        $dashboard_url = 'mechanical_dashboard.php';
        break;
    default:
        die("<p>Invalid branch selected. Submission failed.</p>");
}


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
            <!--<p>Phone: <strong><?= htmlspecialchars($request['phone']); ?></strong></p>-->
            <p>Email: <strong><?= htmlspecialchars($request['email']); ?></strong></p>
            <!--<p>Location: <strong><?= htmlspecialchars($request['location']); ?></strong></p>-->
            <p>Date: <strong><?= htmlspecialchars($request['date']); ?></strong></p>
            <br>
            <p>I would be grateful if you could provide me with the required certificate at the earliest. Thank you for your time and consideration.</p>
            <br>
            <p>Yours sincerely,</p>
            <p><strong><?= htmlspecialchars($request['student_name']); ?></strong></p>
        </div>
    </div>
    <div class="button-container">
        <form id="submit-form" action="<?= $dashboard_url; ?>" method="POST">
            <input type="hidden" name="request_id" value="<?= $request_id; ?>">
            <button type="submit">Submit</button>
        </form>
        <button onclick="window.print()">Print</button>
        <!-- <form action="generate_application_pdf.php" method="POST" target="_blank">
            <input type="hidden" name="request_id" value="<?= $request_id; ?>">
            <button type="submit">Save as PDF</button>
        </form> -->
    </div>

    <div class="popup" id="popup">Request Submitted Successfully!</div>

    <script>
        document.getElementById('submit-form').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('popup').style.display = 'block';
            document.getElementById('content').classList.add('blurred');

            let recipientEmail = "<?= $request['email']; ?>";
            let studentName = "<?= $request['student_name']; ?>";
            let mailtoLink = `mailto:${recipientEmail}?subject=Bonafide%20Request%20Submitted&body=Hello%20${encodeURIComponent(studentName)},%0A%0AYour%20request%20for%20a%20Bonafide%20Certificate%20has%20been%20submitted%20successfully.%20You%20will%20be%20notified%20once%20it%20is%20processed.%0A%0AThank%20you.`;
            
            window.location.href = mailtoLink;

            setTimeout(() => {
                document.getElementById('popup').style.display = 'none';
                document.getElementById('content').classList.remove('blurred');
                document.getElementById('submit-form').submit();
                window.location.href = 'copy.php'; // Redirect to copy.php
            }, 2000);
        });
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
