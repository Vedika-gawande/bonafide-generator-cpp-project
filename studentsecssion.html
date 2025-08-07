<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bonafide Certificate Requests</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .new-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: white;
      z-index: 1000;
      border-bottom: 1px solid #ccc;
    }

    .table-container {
      width: 100%;
      margin: auto;
      margin-top: 80px; /* Adjust this value based on the height of your header */
      text-align: center;
    }

    .title {
      font-size: 22px;
      margin-bottom: 20px;
      font-family: "Times New Roman", Times, serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-family: "Times New Roman", Times, serif;
    }

    table, th, td {
      border: 1px solid black;
    }

    th, td {
      padding: 8px;
      text-align: center;
    }

    th {
      background-color: #5E7EB6;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 5px;
    }

    .action-buttons button {
      padding: 5px 10px;
      font-size: 12px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      color: white;
    }

    .view-button {
      background-color: #6c80c9; /* Green */
    }

    .send-button {
      background-color: #92beec; /* Blue */
    }

    .certificate-container {
      display: none;
      width: 80%;
      margin: 20px auto;
      border: 1px solid #000;
      padding: 20px;
      font-family: "Times New Roman", Times, serif;
    }

    .certificate-title {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .certificate-content {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .certificate-signature {
      text-align: right;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <header class="new-header">
    <div class="new-nav">
      <div class="new-container1">
        <div class="new-btn"><a href="https://gpyavatmal.ac.in/gpy/" style="text-decoration: none; color:black;">Home</a></div>
        <a class="new-btn" href="about.html" style="text-decoration: none;">About</a>
        <a class="new-btn" href="help.html" style="text-decoration: none;">Help</a>
        <svg class="outline" overflow="visible" width="400" height="60" viewBox="0 0 400 60" xmlns="http://www.w3.org/2000/svg">
          <rect class="rect" pathLength="100" x="0" y="0" width="400" height="60" fill="transparent" stroke-width="5"></rect>
        </svg>
      </div>
    </div>
  </header>
  
  <div class="table-container">
    <h1 class="title">Bonafide Certificate Requests</h1>
    <table id="student-table">
      <thead>
        <tr>
          <th>Serial Number</th>
          <th>Full Name</th>
          <th>Phone Number</th>
          <th>Enrollment Number</th>
          <th>Date of Request</th>
          <th>Branch</th>
          <th>Year</th>
          <th>Email</th>
          <th>Location</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>

  <div class="certificate-container" id="certificate-container">
    <div class="certificate-title">BONAFIDE CERTIFICATE</div>
    <div class="certificate-content">
      Certified that <span id="student-name"></span> is/was a bona-fide student of the institute studying in <span id="student-year"></span> year of the diploma course in <span id="student-branch"></span> during the year <span id="student-date"></span>.
      <br><br>
      As per my knowledge, he/she bears a good moral character. This Certificate is issued for the purpose of <span id="certificate-purpose"></span> as per his/her application date <span id="application-date"></span>.
    </div>
    <div class="certificate-signature">
      Principal<br>
      Government Polytechnic, Yavatmal
    </div>
  </div>

  <script>
    function populateTable() {
      const tableBody = document.getElementById('student-table').getElementsByTagName('tbody')[0];
      let applications = JSON.parse(localStorage.getItem('applications')) || [];

      // Sorting by date (oldest first), then by name (A-Z)
      applications.sort((a, b) => {
        const dateA = new Date(a.date);
        const dateB = new Date(b.date);
        
        if (dateA < dateB) return -1;
        if (dateA > dateB) return 1;
        
        return a.name.localeCompare(b.name);
      });

      tableBody.innerHTML = ""; // Clear existing rows

      applications.forEach((applicant, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${index + 1}</td>
          <td>${applicant.name}</td>
          <td>${applicant.phone}</td>
          <td>${applicant.enrollment}</td>
          <td>${applicant.date}</td>
          <td>${applicant.branch}</td>
          <td>${applicant.year}</td>
          <td>${applicant.email}</td>
          <td>${applicant.location}</td>
          <td>
            <div class="action-buttons">
              <button class="view-button" onclick="viewApplication(${index})">View</button>
              <button class="send-button" onclick="sendApplication(${index})">Generate</button>
            </div>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }

    function viewApplication(index) {
      const applications = JSON.parse(localStorage.getItem('applications')) || [];
      const applicant = applications[index];

      if (!applicant) return;

      // Store the selected application data in local storage
      localStorage.setItem('selectedApplicationDetails', JSON.stringify(applicant));

      // Redirect to application view page
      window.location.href = 'application.html';
    }

    function sendApplication() {
      // Retrieve the stored application data
      const applicant = JSON.parse(localStorage.getItem('selectedApplicationDetails'));

      if (!applicant) return;

      // Populate the bonafide certificate with the selected application's data
      document.getElementById('student-name').textContent = applicant.name;
      document.getElementById('student-year').textContent = applicant.year;
      document.getElementById('student-branch').textContent = applicant.branch;
      document.getElementById('student-date').textContent = applicant.date.split('-')[0];
      document.getElementById('certificate-purpose').textContent = "__________"; // Specify the purpose here
      document.getElementById('application-date').textContent = applicant.date;

      // Display the bonafide certificate
      document.getElementById('certificate-container').style.display = 'block';

      // Optionally, hide the table
      document.querySelector('.table-container').style.display = 'none';
    }
    
    window.onload = () => {
      populateTable();

      // Check if there are selected application details and generate the bonafide certificate
      if (localStorage.getItem('selectedApplicationDetails')) {
        sendApplication();
      }
    };
  </script>
</body>
</html>
