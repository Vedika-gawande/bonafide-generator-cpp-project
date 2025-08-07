<?php
// Start a session if needed (optional)
session_start();

// Test generating a mailto link
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Replace this with your Outlook email address for testing
    $recipientEmail = "vedikagawande91@gmail.com";

    $subject = "Test Email with Link";
    $body = "This is a test email to check clickable links in Outlook.%0D%0A%0D%0AClick the link below to test:%0D%0Ahttps://www.google.com";

    echo "<h2>Email Link Test</h2>";
    echo "<p>Click the link below to open your email client:</p>";
    echo "<a href='mailto:$recipientEmail?subject=$subject&body=$body'>Send Test Email via Mail Client</a>";
} else {
    echo "Invalid request method.";
}
?>
