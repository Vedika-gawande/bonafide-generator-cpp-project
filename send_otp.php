<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    $subject = "Your OTP for Verification";
    $message = "Your OTP is: " . $otp . "\nPlease enter this OTP to complete verification.";
    $headers = "From: noreply@yourdomain.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "OTP sent successfully!";
    } else {
        echo "Failed to send OTP.";
    }
}
?>
