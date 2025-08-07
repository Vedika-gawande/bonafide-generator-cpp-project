<?php
// Passwords to be used
$password1 = "876655"; // Password for tchr_admin_123
$password2 = "876655"; // Password for faculty_lead_01

// Generate hashed passwords
$hashed_password1 = password_hash($password1, PASSWORD_BCRYPT);
$hashed_password2 = password_hash($password2, PASSWORD_BCRYPT);

// Display the hashed passwords
echo "Hashed Password for tchr_admin_123: " . $hashed_password1 . "\n";
echo "Hashed Password for faculty_lead_01: " . $hashed_password2 . "\n";
?>
