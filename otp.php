<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = $_POST["otp"];

    if ($enteredOtp == $_SESSION["otp"]) {
        echo json_encode(["status" => "success", "message" => "OTP Verified! Redirecting..."]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid OTP. Please try again."]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter OTP</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="new-header">
    <div class="new-nav">
      <div class="new-container1">
        <div class="new-btn">Home</div>
        <div class="new-btn">About</div>
        <div class="new-btn">Help</div>
        <svg class="outline" overflow="visible" width="400" height="60" viewBox="0 0 400 60" xmlns="http://www.w3.org/2000/svg">
          <rect class="rect" pathLength="100" x="0" y="0" width="400" height="60" fill="transparent" stroke-width="5"></rect>
        </svg>
      </div>
    </div>
  </header>

  <main class="main-content">
    <form class="otp-Form" id="otpForm">
      <span class="mainHeading">Enter OTP</span>
      <p class="otpSubheading">We have sent a verification code to your mobile number</p>
      <div class="inputContainer">
        <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input1">
        <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input2">
        <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input3">
        <input required="required" maxlength="1" type="text" class="otp-input" id="otp-input4"> 
      </div>
      <button class="verifyButton" type="submit" >Verify</button>
      <p class="resendNote">Didn't receive the code? <button class="resendBtn">Resend Code</button></p>
    </form>
  </main>

  <script>
    document.getElementById('otpForm').addEventListener('submit', function(event) {
      event.preventDefault(); // Prevent form submission

      // Here you should add your OTP verification logic
      // For demonstration purposes, let's assume the OTP is always correct

      // If OTP is verified successfully, redirect to registration page
      window.location.href = 'registration.php';
    });
  </script>
</body>
</html>
