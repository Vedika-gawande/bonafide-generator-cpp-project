<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Approval</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #E6EAF3;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #5E7EB6;
            color: white;
        }
        .sign {
            background-color: blue;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .status {
            font-weight: bold;
            text-transform: capitalize;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Principal Approval Panel</h1>
        <table>
            <thead>
                <tr>
                    <th>Serial No</th>
                    <th>Student Name</th>
                    <th>Enrollment</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="applicationsTable"></tbody>
        </table>
    </div>

    <script>
        function loadApplications() {
            let applications = JSON.parse(localStorage.getItem('applications')) || [];
            let tableBody = document.getElementById('applicationsTable');
            tableBody.innerHTML = '';

            applications.forEach((app, index) => {
                let statusColor = app.status === 'Signed' ? 'blue' : 'gray';
                let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${app.name}</td>
                    <td>${app.enrollment}</td>
                    <td>${app.reason}</td>
                    <td class="status" style="color: ${statusColor};">${app.status || 'Pending'}</td>
                    <td>
                        <button class="sign" onclick="signApplication(${index})">Sign</button>
                    </td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        }

        function signApplication(index) {
            let applications = JSON.parse(localStorage.getItem('applications')) || [];
            applications[index].status = 'Signed';
            localStorage.setItem('applications', JSON.stringify(applications));
            alert('Application Signed Successfully!');
            loadApplications();
        }

        window.onload = loadApplications;
    </script>
</body>
</html>
