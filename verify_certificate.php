<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'college_portal');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the certificate ID from the URL
if (isset($_GET['id'])) {
    $certificateID = $_GET['id'];

    // Fetch certificate details from the database
    $stmt = $conn->prepare("SELECT student_id, student_name, course, issue_date FROM certificates WHERE certificate_id = ?");
    $stmt->bind_param("s", $certificateID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h1>Certificate Verified ✅</h1>";
        echo "<p><strong>Student ID:</strong> " . $row['student_id'] . "</p>";
        echo "<p><strong>Name:</strong> " . $row['student_name'] . "</p>";
        echo "<p><strong>Course:</strong> " . $row['course'] . "</p>";
        echo "<p><strong>Issue Date:</strong> " . $row['issue_date'] . "</p>";
        echo "<p>The certificate is valid and issued by the college.</p>";
    } else {
        echo "<h1>Certificate Not Found ❌</h1>";
        echo "<p>The certificate ID is invalid or has been tampered with.</p>";
    }

    $stmt->close();
} else {
    echo "<h1>No Certificate ID Provided ❗</h1>";
}

$conn->close();
?>
