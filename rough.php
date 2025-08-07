<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save & Send PDF via Email</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #content {
            padding: 20px;
            border: 1px solid #ccc;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin: 10px;
        }
    </style>
</head>
<body>

<h2>Bonafide Certificate</h2>

<div id="content">
    <h3 style="text-align: center;">GOVERNMENT POLYTECHNIC YAVATMAL</h3>
    <p style="text-align: center;">DHAMANGAON ROAD, YAVATMAL – 445001</p>
    <p style="text-align: center;">Web Site: www.gpyavatmal.ac.in | E-mail: office.grwpyavatmal@dtemaharashtra.gov.in</p>

    <p>No. GPY/SS/Bonafide/2025 (ID: 151)</p>
    <p>Date: <b>12-03-2025</b></p>

    <h3 style="text-align: center;">BONAFIDE CERTIFICATE</h3>
    <p>Certified that <b>SURYAWANSHI PRASHANT KISAN</b> is a bona-fide student of the institute studying in <b>2nd Year</b> of the diploma course in <b>Electronics Engineering</b> during the year <b>2024 - 2025</b>.</p>
    <p>As per my knowledge, they bear a good moral character. This certificate is issued for the purpose of applying for scholarships as per their application date <b>12-03-2025</b>.</p>

    <p style="text-align: right;">Principal<br>Government Polytechnic, Yavatmal</p>
</div>

<button class="btn" onclick="saveAsPDF()">Save as PDF</button>
<button class="btn" onclick="sendEmail()">Send as Email</button>

<script>
    const { jsPDF } = window.jspdf;

    function saveAsPDF() {
        const pdf = new jsPDF();
        
        // Add institute logo
        const logo = 'gpy.jpg'; // Path to your logo image
        const stamp = 'stamp.png'; // Path to your stamp image
        const sign = 'mechanical.jpg'; // Path to principal’s signature
        
        pdf.addImage(logo, 'PNG', 80, 10, 50, 20); // Add logo at the top

        pdf.setFontSize(12);
        pdf.text("GOVERNMENT POLYTECHNIC YAVATMAL", 10, 40);
        pdf.text("DHAMANGAON ROAD, YAVATMAL – 445001", 10, 50);
        pdf.text("Web Site: www.gpyavatmal.ac.in | E-mail: office.grwpyavatmal@dtemaharashtra.gov.in", 10, 60);
        
        pdf.text("No. GPY/SS/Bonafide/2025 (ID: 151)", 10, 80);
        pdf.text("Date: 12-03-2025", 10, 90);
        
        pdf.setFontSize(16);
        pdf.text("BONAFIDE CERTIFICATE", 70, 110);
        
        pdf.setFontSize(12);
        pdf.text("Certified that SURYAWANSHI PRASHANT KISAN is a bona-fide student of the institute studying in", 10, 130);
        pdf.text("2nd Year of the diploma course in Electronics Engineering during the year 2024 - 2025.", 10, 140);
        
        pdf.text("As per my knowledge, they bear a good moral character.", 10, 160);
        pdf.text("This certificate is issued for the purpose of applying for scholarships as per their application date 12-03-2025.", 10, 170);
        
        // Add stamp and signature
        pdf.addImage(stamp, 'PNG', 10, 200, 40, 40);
        pdf.addImage(sign, 'PNG', 140, 200, 40, 20);
        
        pdf.text("Principal", 150, 230);
        pdf.text("Government Polytechnic, Yavatmal", 110, 240);
        
        pdf.save("Bonafide_Certificate.pdf");
    }

    function sendEmail() {
        const content = document.getElementById("content").innerText;

        const blob = new Blob([content], { type: 'application/pdf' });
        const pdfUrl = URL.createObjectURL(blob);

        const recipientEmail = "recipient@example.com";  // Add recipient email here
        const subject = encodeURIComponent("Bonafide Certificate");
        const body = encodeURIComponent(
            "Please find the Bonafide Certificate attached.\n\n" +
            "Download it here: " + pdfUrl + "\n\n" +
            "Best regards,\nGovernment Polytechnic Yavatmal"
        );

        window.location.href = `mailto:${recipientEmail}?subject=${subject}&body=${body}`;
    }
</script>

</body>
</html>
