<?php
session_start(); 
$host = 'localhost';
$dbname = 'student_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Initialize error message
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_type'])) {
    $user_type = $_POST['user_type'];

    // Student Login
    if ($user_type === 'student' && isset($_POST['enrollment_number'])) {
        $enrollment_number = trim($_POST['enrollment_number']);
        $stmt = $pdo->prepare("SELECT * FROM students WHERE enrollment_number = :enrollment_number");
        $stmt->bindParam(':enrollment_number', $enrollment_number, PDO::PARAM_STR);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $_SESSION['user_type'] = 'student';
            $_SESSION['enrollment_number'] = $enrollment_number; // Store enrollment for validation
            header('Location: otpgen.php'); // Redirect to OTP generation page
            exit();
        } else {
            $error_message = "❌ Enrollment number not found.";
        }
    // Teacher Login (No logic for fetching teacher data)
    } elseif ($user_type === 'teacher' && isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        // Default usernames and strong passwords for teachers
        $teachers = [
            'Computer' => 'StR0ng@2025P@ss',
            'Principal' => 'StR0ng@2025P@ss2',
            'Civil' => 'SecUr3@2025Pass3',
            'Electrical' => 'EduM@st3r#2025',
            'Electronics' => 'Te@ch3rL0g!n2025',
            'Mechanical' => 'Adm1n@Portal2025'
        ];
    
        // Check if username and password match for the teachers
        if (array_key_exists($username, $teachers) && $password === $teachers[$username]) {
            $_SESSION['user_type'] = 'teacher';
            $_SESSION['username'] = $username;
    
            // Redirect based on the username
            switch ($username) {
                case 'Computer':
                    header('Location: teacher_dashboard.php');
                    break;
                case 'Principal':
                    header('Location: principal_dashboard.php');
                    break;
                case 'Civil':
                    header('Location: civil_dashboard.php');
                    break;
                case 'Electrical':
                    header('Location: electrical_dashboard.php');
                    break;
                case 'Electronics':
                    header('Location: electronics_dashboard.php');
                    break;
                case 'Mechanical':
                    header('Location: mechanical_dashboard.php');
                    break;
            }
            exit();
        } else {
            $error_message = "❌ Invalid username or password.";
        }
    }
}    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Login Portal</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #F5EDED;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
            width: 450px;
            text-align: center;
        }
        h2 {
            color: #333;
            font-size: 28px;
            text-align: left;
        }
        .toggle-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .toggle-button {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #1e3c72;
        }
        .toggle-button img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .toggle-button:hover {
            transform: scale(1.1);
        }
        .input-group {
            margin-bottom: 15px;
            display: none;
        }
        label {
            font-size: 18px;
            font-weight: bold;
            color:rgb(5, 1, 26);
            display: block;
            text-align: left;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #1e3c72;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
        }
        .btn {
            background:rgb(94, 86, 240);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
            font-size: 16px;
        }
        .btn:hover {
            background: #2a5298;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
    <script>
        // Function to show the correct form fields
        function showForm(userType) {
            document.getElementById('student-fields').style.display = (userType === 'student') ? 'block' : 'none';
            document.getElementById('teacher-fields').style.display = (userType === 'teacher') ? 'block' : 'none';
            document.getElementById('user_type').value = userType;

            // Enable required fields correctly
            if (userType === 'student') {
                document.getElementById('enrollment_number').setAttribute("required", "true");
                document.getElementById('username').removeAttribute("required");
                document.getElementById('password').removeAttribute("required");
            } else {
                document.getElementById('username').setAttribute("required", "true");
                document.getElementById('password').setAttribute("required", "true");
                document.getElementById('enrollment_number').removeAttribute("required");
            }

            // Store the selected user type in sessionStorage
            sessionStorage.setItem('selectedUserType', userType);
        }

        // Preserve selected form after error
        document.addEventListener("DOMContentLoaded", function () {
            let savedUserType = sessionStorage.getItem('selectedUserType') || 'student'; // Default to student
            showForm(savedUserType);
        });
    </script>
</head>
<body>
<div class="container">
    <h2>Welcome!<br>Sign in to Continue</h2>
    <form method="POST" action="">
        <input type="hidden" name="user_type" id="user_type">
        <div class="toggle-container">
            <div class="toggle-button" onclick="showForm('student')" title="Login as Student">
                <img src="student.jpg" alt="Student">
            </div>
            <div class="toggle-button" onclick="showForm('teacher')" title="Login as Teacher">
                <img src="teacher.jpg" alt="Teacher">
            </div>
        </div>
        <div id="student-fields" class="input-group">
            <label for="enrollment_number">Enrollment Number</label>
            <input type="text" id="enrollment_number" name="enrollment_number">
        </div>
        <div id="teacher-fields" class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>
        <input type="submit" class="btn" value="Login">
        <?php if ($error_message): ?>
            <p class="error-message"><?= $error_message; ?></p>
        <?php endif; ?>
    </form>
</div>
<div class="button-container">
    <a href="http://gpyavatmal.ac.in/" class="styled-button" target="_blank">Home</a>
    <a href="about.html" class="styled-button">About</a>
    <a href="help.html" class="styled-button">Help</a>
</div>
<style>
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
</body>
</html>