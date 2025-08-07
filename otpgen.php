<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.3);
            width: 450px;
            text-align: center;
        }
        h2 {
            color: #1e3c72;
            font-size: 26px;
        }
        label {
            font-size: 18px;
            font-weight: bold;
            color:rgb(1, 6, 16);
            display: block;
            text-align: left;
            margin-bottom: 5px;
        }
        input {
            width: 95%;
            padding: 12px;
            border: 2px solid #1e3c72;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
        }
        .btn {
            background:rgb(91, 67, 230);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 101%;
            margin-top: 15px;
        }
        .btn:hover {
            background:rgb(92, 46, 207);
        }
        .otp-section {
            display: none;
            margin-top: 20px;
        }
        .message {
            color: green;
            font-size: 16px;
            margin-top: 10px;
        }
        .error {
            color: red;
            font-size: 16px;
            margin-top: 10px;
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
        let generatedOTP;
        function generateOTP() {
            return Math.floor(1000 + Math.random() * 9000);
        }
        function validateEmail(email) {
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            return emailPattern.test(email);
        }
        function sendOTP() {
            let recipientEmail = document.getElementById('recipientEmail').value.trim();
            const errorMessage = document.getElementById('errorMessage');

            if (!validateEmail(recipientEmail)) {
                errorMessage.innerText = "Please enter a valid email address.";
                return;
            }

            sessionStorage.setItem("userEmail", recipientEmail);

            generatedOTP = generateOTP();
            let mailtoLink = `mailto:${recipientEmail}?subject=Your%20OTP%20for%20Verification&body=Your%20OTP%20is%3A%20${generatedOTP}`;
            window.location.href = mailtoLink;

            

            document.getElementById('otpSection').style.display = 'block';
            document.getElementById('message').innerText = "OTP sent to your email!";
            errorMessage.innerText = "";
        }

        function verifyOTP() {
            let enteredOTP = document.getElementById('otpInput').value.trim();
            if (enteredOTP == generatedOTP) {
                let email = sessionStorage.getItem("userEmail");

                fetch('store_email.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `email=${email}`
                }).then(() => {
                    window.location.href = 'registration.php';
                });
            } else {
                alert('Incorrect OTP. Please try again.');
            }
        }
    </script>
</head>
<body>
<div class="button-container">
    <a href="http://gpyavatmal.ac.in/" class="styled-button" target="_blank">Home</a>
    <a href="about.html" class="styled-button">About</a>
    <a href="help.html" class="styled-button">Help</a>
</div>

    <div class="container">
        <h2>Email Verification</h2>
        <label for="recipientEmail">Enter Your Email:</label></br>
        <input type="email" id="recipientEmail" required>
        <p id="errorMessage" class="error"></p>
        <button class="btn" onclick="sendOTP()">Send OTP</button>
        <p id="message" class="message"></p>

        <div id="otpSection" class="otp-section">
            <label for="otpInput">Enter OTP:</label>
            <input type="text" id="otpInput">
            <button class="btn" onclick="verifyOTP()">Verify OTP</button>
        </div>
    </div>
</body>
</html>
