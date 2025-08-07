<?php
// WhatsApp PDF sender

// Recipient's phone number with country code
$phone = '918766558409'; 

// URL of the generated PDF certificate
$pdfUrl = 'http://localhost/college_portal/bonafide_certificates/certificate.pdf';

// Encode the message text
$message = urlencode("Your bonafide certificate is ready! Download it here: $pdfUrl");

// Create the WhatsApp link
$whatsappLink = "https://api.whatsapp.com/send?phone=$phone&text=$message";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send PDF via WhatsApp</title>
</head>
<body>
    <h2>Send Bonafide Certificate via WhatsApp</h2>
    <a href="<?php echo $whatsappLink; ?>" target="_blank">
        <button>Send via WhatsApp</button>
    </a>
</body>
</html>
