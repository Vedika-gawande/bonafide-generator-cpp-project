<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
}


global $pdo;

function fetchApprovedRequests($searchQuery, $pdo) {
    $sqlApproved = "SELECT * FROM bonafide_requests WHERE status = 'Approved' AND signed = 0";
    if (!empty($searchQuery)) {
        $sqlApproved .= " AND (student_name LIKE :search OR enrollment_number LIKE :search)";
    }
    $stmtApproved = $pdo->prepare($sqlApproved);
    if (!empty($searchQuery)) {
        $stmtApproved->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
    }
    $stmtApproved->execute();
    return $stmtApproved->fetchAll(PDO::FETCH_ASSOC);
}

function countSignedRequests($pdo) {
    $stmtSigned = $pdo->query("SELECT COUNT(*) AS signed_count FROM bonafide_requests WHERE signed = 1");
    $result = $stmtSigned->fetch(PDO::FETCH_ASSOC);
    return $result['signed_count'] ?? 0;
}

$approvedRequests = fetchApprovedRequests($searchQuery, $pdo);
$pendingCount = count($approvedRequests);
$signedCount = countSignedRequests($pdo);

function fetchStudentRecords($searchQuery, $pdo) {
    if (!empty($searchQuery)) {
        $sqlStudents = "SELECT * FROM sdetails WHERE name LIKE :search OR enrollment_number LIKE :search";
        $stmtStudents = $pdo->prepare($sqlStudents);
        $stmtStudents->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
        $stmtStudents->execute();
        return $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
    }
    return [];
}

$students = fetchStudentRecords($searchQuery, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Dashboard</title>
    <header>
    <div class="button-container">
    <a href="http://gpyavatmal.ac.in/" class="styled-button" target="_blank">Home</a>
    <a href="about.html" class="styled-button">About</a>
    <a href="help.html" class="styled-button">Help</a>
    <div class="request-buttons">
        <button onclick="showRequests('signed')" class="styled-button">View Signed Requests</button>
    </div>

</div>
</header>
    <link rel="stylesheet" href="styles1.css">
    <script>
        function showRequests(type) {
            const requestData = document.getElementById('requestData');
            requestData.style.display = 'none'; // Hide existing table first
            requestData.innerHTML = '';
            
            setTimeout(() => { // Small delay to make hiding effect smoother
                requestData.style.display = 'block';
                if (type === 'signed') {
                    requestData.innerHTML = `
                        <h3>Signed Requests</h3>
                        <table border="1">
                            <tr>
                                <th>Student Name</th>
                                <th>Enrollment Number</th>
                                <th>Branch</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            <?php foreach ($approvedRequests as $request) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($request['student_name']) ?></td>
                                    <td><?= htmlspecialchars($request['enrollment_number']) ?></td>
                                    <td><?= htmlspecialchars($request['branch']) ?></td>
                                    <td><?= htmlspecialchars($request['year']) ?></td>
                                    <td>
    <?php if ($request['status'] === 'Approved'): ?>
        <p style='color: green;'>Approved by HOD (<?= htmlspecialchars($request['approved_by']) ?>) on <?= date("d-m-Y H:i:s", strtotime($request['approved_at'])) ?></p>
    <?php else: ?>
        <p style='color: red;'>Pending Approval</p>
    <?php endif; ?>
</td>
                                    <td><?= htmlspecialchars($request['date']) ?></td>
                                </tr>
                            <?php } ?>
                        </table>`;
                } else if (type === 'pending') {
                    requestData.innerHTML = `
                        <h3>Pending Requests</h3>
                        <table border="1">
                            <tr>
                                <th>Student Name</th>
                                <th>Enrollment Number</th>
                                <th>Branch</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            <?php foreach ($students as $student) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($student['name']) ?></td>
                                    <td><?= htmlspecialchars($student['enrollment_number']) ?></td>
                                    <td><?= htmlspecialchars($student['branch']) ?></td>
                                    <td><?= htmlspecialchars($student['year']) ?></td>
                                    <td><?= htmlspecialchars($student['status'] ?? 'Pending') ?></td>
                                    <td><?= htmlspecialchars($student['date'] ?? '-') ?></td>
                                </tr>
                            <?php } ?>
                        </table>`;
                }
            }, 200);
        }
    </script>
</head>
<body>
<div class="main-content">
    <h2>Principal Dashboard</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by Name or Enrollment Number" value="<?= htmlspecialchars($searchQuery) ?>">
        <button type="submit">Search</button>
    </form>

    
    <div id="requestData" style="display: none; margin-top: 20px;"></div>
</div>


<script>
    function showSignedRequests() {
        fetch('get_signed_requests.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('requestTitle').innerText = 'Signed Requests';
                document.getElementById('requestContent').innerHTML = data;
                document.getElementById('requestData').style.display = 'block';
            });
    }

    function showPendingRequests() {
        fetch('get_pending_requests.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('requestTitle').innerText = 'Pending Requests';
                document.getElementById('requestContent').innerHTML = data;
                document.getElementById('requestData').style.display = 'block';
            });
    }
    </script>

    <!-- Approved Bonafide Requests -->

    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Enrollment Number</th>
            <th>Email</th>
            <th>Branch</th>
            <th>Year</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php if (count($approvedRequests) > 0): ?>
            <?php foreach ($approvedRequests as $request): ?>
                <tr id="row-<?= $request['id']; ?>">
                    <td><?= $request['id']; ?></td>
                    <td><?= $request['student_name']; ?></td>
                    <td><?= $request['enrollment_number']; ?></td>
                    <td><?= $request['email']; ?></td>
                    <td><?= $request['branch']; ?></td>
                    <td><?= $request['year']; ?></td>
                    <td><?= $request['date']; ?></td>
                    <td>
                    <button class="btn view-btn" onclick="redirectToDashboard(<?= $request['id']; ?>)">View</button>
                        <button class="btn view-btn" onclick="signRequest(<?= $request['id']; ?>)">Sign</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No approved requests found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Student Search Results -->
    <?php if (!empty($searchQuery)): ?>
    <h2>Search Results</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Enrollment Number</th>
            <th>Current Studying Year</th>
            <th>Branch</th>
            <!--<th>Phone Number</th>-->
            <th>Email</th>
            <th>Addmission Year</th>
            <th>Passout Year</th>
            <th>Status</th>
        </tr>
        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['id']; ?></td>
                    <td><?= $student['name']; ?></td>
                    <td><?= $student['enrollment_number']; ?></td>
                    <td><?= $student['current_studying_year']; ?></td>
                    <td><?= $student['branch']; ?></td>
                    <!--<td><?= $student['phone_number']; ?></td>-->
                    <td><?= $student['email']; ?></td>
                    <td><?= $student['admission_year']; ?></td>
                    <td><?= $student['passout_year']; ?></td>
                    <td><?= $student['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No students found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>
</div>
<script>
    function viewRequest(requestId) {
        window.location.href = 'view_request.php?request_id=' + requestId;
    }

function redirectToDashboard(requestId) {
    window.location.href = 'fetch_branch.php?request_id=' + requestId;
}

    function signRequest(requestId) {
    if (confirm("Are you sure you want to sign this request?")) {
        fetch('sign_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'request_id=' + requestId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById("row-" + requestId);
                if (row) {
                    row.remove();
                }
                window.location.href = 'generate_bonafide_certificate.php?request_id=' + requestId;
            } else {
                alert("Failed to sign the request. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error signing request:", error);
            alert("An error occurred. Please try again.");
        });
    }
    
}

</script>


<style>
  .button-container {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

.styled-button {
    background-color: .button-container {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

.styled-button {
    background-color: transparent;
    color: black; /* Text color black */
    border: none;
    padding: 12px 20px; /* Increased size */
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px; /* Slightly larger font */
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s ease;
}

.styled-button:hover {
    background-color: rgb(137, 160, 246); /* Light blue hover color */
    color: black; /* Ensure text stays black on hover */
}
;
    color: black; /* Text color black */
    border: none;
    padding: 12px 20px; /* Increased size */
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px; /* Slightly larger font */
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s ease;
}

.styled-button{
background-color: rgb(217, 212, 234);
}
.styled-button:hover {
    background-color: rgb(173, 187, 236); /* Light blue hover color */
    color: black; /* Ensure text stays black on hover */
}

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            padding: 0;
            background-color: #F5EDED;
        }
        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;s
            margin: 40px;
        }
        
    
    
        h2 {
            color: #333;
        }
        .search-bar {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    .search-bar input {
        padding: 10px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .search-bar button {
        padding: 10px 20px;
        background-color: rgb(142, 163, 231);
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
    }
    .search-bar button:hover {
        background-color:rgb(90, 92, 226);
    }
    .request-buttons {
    position: absolute;
    top: 10px;
    right: -1550px;
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color:rgb(148, 120, 240);
            color: white;
        }
        .approve-btn, .reject-btn, .view-btn {
            color: white;
            padding: 3px 8px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .approve-btn {
            background-color: rgb(159, 135, 238);
        }

        .approve-btn:hover {
            background-color: rgb(78, 56, 224);
        }

        .reject-btn {
            background-color: rgb(159, 135, 238);
        }

        .reject-btn:hover {
            background-color: rgb(78, 56, 224);
        }

        .view-btn {
            background-color:rgb(96, 67, 224);
        }

        .view-btn:hover {
            background-color:rgb(93, 86, 239);
        }
       
  
</style>
    
</body>
</html>
