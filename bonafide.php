<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonafide Certificate</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
            margin: 40px;
        }
        .certificate {
            width: 800px;
            margin: auto;
            border: 2px solid black;
            padding: 40px;
            position: relative;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
        }
        .header img {
            width: 100px;
            position: absolute;
            left: 50px;
            top: 20px;
        }
        .header {
            text-align: center;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0;
            font-size: 14px;
        }
        .line {
            border-bottom: 2px solid black;
            margin: 10px 0;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }
        .content {
            text-align: left;
            font-size: 18px;
            margin-top: 20px;
            line-height: 1.8;
        }
        .signature {
            text-align: right;
            margin-top: 50px;
            font-weight: bold;
        }
        .date {
            text-align: right;
            margin-top: -30px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <img src="logo.png" alt="Institute Logo">
            <h2>GOVERNMENT POLYTECHNIC YAVATMAL</h2>
            <p>DHAMANGAON ROAD, YAVATMAL – 445001</p>
            <p>Web Site: www.gpyavatmal.ac.in | E-mail: office.grwpyavatmal@dtemaharashtra.gov.in</p>
            <div class="line"></div>
            <p style="text-align: left;">No. GPY/SS/Bonafide/20<span id="year"></span></p>
            <p class="date">Date: _________</p>
        </div>
        <div class="title">BONAFIDE CERTIFICATE</div>
        <div class="content">
            Certified that <span id="studentName">__________________</span>,<br>
            is/was a bona-fide student of the institute studying in <span id="courseYear">I/II/III</span> year of the diploma course in <br>
            <span id="department">__________________</span> during the year 20<span id="startYear"></span> - 20<span id="endYear"></span>.
            <br><br>
            As per my knowledge, he/she bears a good moral character. This certificate is issued for the purpose of <span id="purpose">__________________</span> as per his/her application date _________.
        </div>
        <div class="signature">
            Principal<br>
            Government Polytechnic,<br>
            Yavatmal
        </div>
    </div>
    
    <script>
        let currentYear = new Date().getFullYear();
        document.getElementById("year").innerText = currentYear.toString().slice(-2);
        document.getElementById("startYear").innerText = (currentYear - 1).toString().slice(-2);
        document.getElementById("endYear").innerText = currentYear.toString().slice(-2);
    </script>
</body>
</html>
