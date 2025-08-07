<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $gender = $_POST['gender'];

    $updateQuery = "UPDATE students SET gender = '$gender' WHERE student_id = '$student_id'";

    if (mysqli_query($conn, $updateQuery)) {
        echo "Student information updated successfully!";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
