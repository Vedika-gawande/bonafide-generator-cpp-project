<?php
include 'db_connect.php';

$student = null;

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
    } else {
        echo "Student not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
</head>
<body>
<h2>Student Registration Form</h2>

<form action="update_student.php" method="post">
    <label>Student ID:</label>
    <input type="text" name="student_id" value="<?= $student['student_id'] ?? ''; ?>" readonly><br>

    <label>Name:</label>
    <input type="text" name="name" value="<?= $student['name'] ?? ''; ?>" readonly><br>

    <label>Department:</label>
    <input type="text" name="department" value="<?= $student['department'] ?? ''; ?>" readonly><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $student['email'] ?? ''; ?>" readonly><br>

    <label>Gender:</label>
    <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male" <?= ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?= ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
        <option value="Other" <?= ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select><br>

    <button type="submit">Update</button>
</form>

</body>
</html>
