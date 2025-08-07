<?php
include 'db.php';

try {
    $stmt = $pdo->query("SELECT id, student_name, enrollment_number, email, branch, year, date FROM bonafide_requests WHERE status = 'signed'");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($requests) {
        echo "<table border='1'><tr><th>ID</th><th>Student Name</th><th>Enrollment Number</th><th>Email</th><th>Branch</th><th>Year</th><th>Request Date</th></tr>";
        foreach ($requests as $request) {
            echo "<tr><td>" . $request['id'] . "</td><td>" . $request['student_name'] . "</td><td>" . $request['enrollment_number'] . "</td><td>" . $request['email'] . "</td><td>" . $request['branch'] . "</td><td>" . $request['year'] . "</td><td>" . $request['date'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No signed requests found.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>