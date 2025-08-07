<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $enrollment = trim($_POST['enrollment']);
    $date = trim($_POST['date']);
    $branch = trim($_POST['branch']);
    $year = trim($_POST['year']);
    $email = trim($_POST['email']);
    $reason = trim($_POST['reason']) === 'Other' ? trim($_POST['other_reason_text']) : trim($_POST['reason']);
    $username = $_SESSION['username'] ?? 'Guest';

    if (!isset($_SESSION['enrollment_number']) || $_SESSION['enrollment_number'] !== $enrollment) {
        echo "<script>alert('Enrollment number does not match your login session.'); window.location.href='application.php';</script>";
        exit();
    }

    if (empty($name) || empty($enrollment) || empty($date) || empty($branch) || empty($year) || empty($reason) || empty($email)) {
        echo "<script>alert('All fields are required!'); window.location.href='application.php';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href='application.php';</script>";
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE enrollment_number = ? AND status = 'Pending'");
        $stmt->execute([$enrollment]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('A pending request already exists for this enrollment number.'); window.location.href='copy.php';</script>";
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO bonafide_requests (student_name, enrollment_number, date, branch, year, reason, email, username, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$name, $enrollment, $date, $branch, $year, $reason, $email, $username]);

        $request_id = $pdo->lastInsertId();
        echo "<script>alert('Bonafide request submitted successfully! Request ID: {$request_id}'); window.location.href='application.php?request_id={$request_id}';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

$email = $_SESSION['email'] ?? ''; 
?>

<!-- Your form and HTML remains the same -->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bonafide Certificate Request</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      width: 400px;
    }
    .title {
      text-align: center;
      margin-bottom: 20px;
    }
    .input-group {
      margin-bottom: 15px;
    }
    .input-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .input-group input, .input-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    .input-row {
      display: flex;
      gap: 10px;
    }
    .input-row .input-group {
      flex: 1;
    }
    .submit-btn {
      width: 100%;
      padding: 10px;
      background-color:rgb(72, 58, 180);
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    .submit-btn:hover {
      background-color:rgb(40, 26, 110);
    }
    #error-message {
      color: red;
      text-align: center;
    }
    #other-reason {
      display: none;
    }
    /* Button container */
.button-container {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

/* Styled button */
.styled-button {
    background-color:transparent; /* Dark blue background */
    color: black; 
    border: none;
    padding: 12px 20px; /* Increased size */
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px; /* Slightly larger font */
    text-decoration: none; /* Remove underline */
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s ease;
}

/* Button hover effect */
.styled-button:hover {
    background-color:rgb(129, 129, 240); /* Bright blue on hover */
}
  </style>
  <script>
    function fetchStudentData() {
        const enrollment = document.getElementById('enrollment').value.trim();
        if (enrollment === '') return;

        fetch('fetch_student.php?enrollment=' + encodeURIComponent(enrollment))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('error-message').innerText = data.error;
            } else {
                const sessionEnrollment = "<?= $_SESSION['enrollment_number'] ?? '' ?>";
                
                if (sessionEnrollment && sessionEnrollment !== enrollment) {
                    document.getElementById('error-message').innerText = "Enrollment number mismatch! Please use the same enrollment number as logged in.";
                    return;
                }

                document.getElementById('name').value = data.full_name || '';
                document.getElementById('branch').value = data.branch || '';
                document.getElementById('year').value = data.year || '';
                document.getElementById('email').value = data.email || document.getElementById('email').value;
                document.getElementById('error-message').innerText = '';
            }
        })
        .catch(error => {
            document.getElementById('error-message').innerText = 'Error fetching student data.';
            console.error('Error fetching student data:', error);
        });
    }

    function validateForm() {
        const fields = ['name', 'enrollment', 'date', 'branch', 'year', 'reason'];
        
        for (let field of fields) {
            if (document.getElementById(field).value.trim() === '') {
                alert("Please fill all required fields before submitting.");
                return false;
            }
        }

        if (document.getElementById('reason').value === 'Other' && 
            document.getElementById('other-reason-text').value.trim() === '') {
            alert("Please specify the reason under 'Other'.");
            return false;
        }

        return true;
    }

    function toggleOtherReason() {
        document.getElementById('other-reason').style.display = 
            document.getElementById('reason').value === 'Other' ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('request-form').addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault();
            }
        });
    });
  </script>
</head>
<body>
<div class="button-container">
    <a href="http://gpyavatmal.ac.in/" class="styled-button" target="_blank">Home</a>
    <a href="about.html" class="styled-button">About</a>
    <a href="help.html" class="styled-button">Help</a>
</div>

  <div class="form-container">
    <h1 class="title">Bonafide Certificate Request Form</h1>
    <form id="request-form" method="POST">
      <div class="input-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="input-group">
        <label for="enrollment">Enrollment Number</label>
        <input type="text" id="enrollment" name="enrollment" required onblur="fetchStudentData()">
      </div>
      <div class="input-group">
        <label for="date">Date of Request</label>
        <input type="date" id="date" name="date" required>
      </div>
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          let today = new Date().toISOString().split('T')[0];
          let dateInput = document.getElementById("date");
          dateInput.setAttribute("min", today);
          dateInput.setAttribute("max", today);
        });
      </script>
      <div class="input-group">
        <label for="branch">Branch</label>
        <input type="text" id="branch" name="branch" required>
      </div>
      <div class="input-group">
        <label for="year">Year</label>
        <input type="text" id="year" name="year" required>
      </div>
      <div class="input-group">
        <label for="reason">Reason for Bonafide Certificate</label>
        <select id="reason" name="reason" onchange="toggleOtherReason()" required>
          <option value="Applying for scholarships">Applying for scholarships</option>
          <option value="Securing education loans">Securing education loans</option>
          <option value="Applying for visas">Applying for visas</option>
          <option value="Hostel Admission">Hostel Admission</option>
          <option value="Passport Application">Passport Application</option>
          <option value="Internship Application">Internship Application</option>
          <option value="Company Verification">Company Verification</option>
          <option value="Driving License Registration">Driving License Registration</option>
          <option value="Bank Account Opening">Bank Account Opening</option>
          <option value="Government Document Verification">Government Document Verification</option>
          <option value="Job Application">Job Application</option>
          <option value="Study Abroad Application">Study Abroad Application</option>
          <option value="Exam Fee Waiver">Exam Fee Waiver</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="input-group" id="other-reason" style="display: none;">
        <label for="other-reason-text">Specify Other Reason</label>
        <input type="text" id="other-reason-text" name="other_reason_text" placeholder="Enter your reason">
      </div>
      <div class="input-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <button type="submit" class="submit-btn">Submit Request</button>
    </form>
    <p id="error-message" style="color: red;"></p>
  </div>
</body>

</html>
