<?php
session_start();
require 'db.php';

$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id']) && isset($_POST['status'])) {
    $requestId = $_POST['request_id'];
    $status = $_POST['status'];

    // Check if the request is already approved and present in principal_dashboard
    $checkStmt = $pdo->prepare("SELECT id FROM bonafide_requests WHERE id = ? AND status = 'Approved'");
    $checkStmt->execute([$requestId]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Request already approved and present in Principal Dashboard.']);
    } else {
        // Update the status if not already approved
        $stmt = $pdo->prepare("UPDATE bonafide_requests SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $requestId])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    exit;
}

// Fetch counts for each status
$countApproved = $pdo->query("SELECT COUNT(*) FROM bonafide_requests WHERE status = 'Approved' and branch='Civil Engineering'" )->fetchColumn();
$countRejected = $pdo->query("SELECT COUNT(*) FROM bonafide_requests WHERE status = 'Rejected'and branch='Civil Engineering'")->fetchColumn();
$countPending = $pdo->query("SELECT COUNT(*) FROM bonafide_requests WHERE status = 'Pending'and branch='Civil Engineering'")->fetchColumn();
$totalRequests = $pdo->query("SELECT COUNT(*) FROM bonafide_requests WHERE branch='Civil Engineering'")->fetchColumn();

// Fetch requests by status
function fetchRequestsByStatus($pdo, $status) {
    if ($status === 'All') {
        $stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE branch = 'Civil Engineering'");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE status = ? AND branch = 'Civil Engineering'");
        $stmt->execute([$status]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch pending requests
$stmt = $pdo->prepare("SELECT * FROM bonafide_requests WHERE status = 'Pending' AND branch = 'Civil Engineering'");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Function to send email
function sendEmail($email, $subject, $body) {
    $mailtoLink = "mailto:$email?subject=$subject&body=$body";
    echo "<script>window.location.href='$mailtoLink';</script>";
}

// Function to send message
function sendMessage( $message) {
    echo "<script>alert('SMS sent to : $message');</script>";
}

$student = [];
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$student = [];
if (!empty($searchQuery)) {
    $sqlStudent = "SELECT * FROM sdetails WHERE enrollment_number = :searchQuery OR name LIKE :searchQueryLike";
    $stmtStudent = $pdo->prepare($sqlStudent);

    // Check if the input is numeric to handle enrollment number
    if (is_numeric($searchQuery)) {
        $stmtStudent->bindParam(':searchQuery', $searchQuery, PDO::PARAM_INT);
    } else {
        $stmtStudent->bindParam(':searchQuery', $searchQuery, PDO::PARAM_STR);
    }

    // Handle LIKE for name search
    $searchQueryLike = "%$searchQuery%";
    $stmtStudent->bindParam(':searchQueryLike', $searchQueryLike, PDO::PARAM_STR);
    
    $stmtStudent->execute();
    $student = $stmtStudent->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="styles1.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Teacher Dashboard</title>
    
    <script>
        function sendNotification(email,status, rowId) {
            let subject = status === 'Approved' ? 'Bonafide Request Approved' : 'Bonafide Request Rejected';
            let body = `Your Bonafide request has been ${status} by HOD `;
            let message = `Your Bonafide request has been ${status} by HOD`;

            // Send email
            let mailtoLink = `mailto:${email}?subject=${subject}&body=${body}`;
            window.location.href = mailtoLink;

            // Send message
            sendMessage( message);

            // Hide the row
            document.getElementById('row-' + rowId).style.display = 'none';

            // Update status in database
            updateRequestStatus(rowId, status);
        }

        function updateRequestStatus(requestId, status) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `request_id=${requestId}&status=${status}`
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Show the actual server response
        if (data.includes("Status updated successfully")) {
            window.location.reload(); // Only reload if update was successful
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
    });
}
function updateRequestStatus(requestId, status) {
    if (confirm("Are you sure you want to " + status.toLowerCase() + " this request?")) {
        fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'request_id=' + requestId + '&status=' + status
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload(); // Refresh the dashboard
        });
    }
}
        function sendMessage(message) {
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });
        }
    </script>
</head>
<body>
<header class="new-header">
    <div class="new-nav">
        <div class="new-container1">
            <div class="new-btn"><a href="https://gpyavatmal.ac.in/gpy/" style="text-decoration: none; color: black;">Home</a></div>
            <a class="new-btn" href="about.html">About</a>
            <a class="new-btn" href="help.html">Help</a>
            <svg class="outline" overflow="visible" width="400" height="60" viewBox="0 0 400 60" xmlns="http://www.w3.org/2000/svg">
                <rect class="rect" pathLength="100" x="0" y="0" width="400" height="60" fill="transparent" stroke-width="5"></rect>
            </svg>
            <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by Name or Enrollment Number" value="<?= htmlspecialchars($searchQuery) ?>">
        <button type="submit">Search</button>
    </form>
        </div>
    </div>
</header>
<div class="main-content">
    <h2>Pending Bonafide Requests</h2>
    <!-- Search Bar -->
    
    <h2 class="text-center">Request Counts</h2>
<div class="d-flex justify-content-center">
    <div class="btn-group" role="group">
        <button class="btn" style="background-color:rgba(157, 129, 241, 0.76); color: #fff;" onclick="showApprovedRequests()">Approved Requests: <?php echo $countApproved; ?></button>
        <button class="btn" style="background-color:rgba(134, 98, 241, 0.76); color: #fff;" onclick="showData('rejected')">Rejected Requests: <?php echo $countRejected; ?></button>
        <button class="btn" style="background-color:rgba(95, 48, 237, 0.76); color: #000;" onclick="showData('pending')">Pending Requests: <?php echo $countPending; ?></button>
        <button class="btn" style="background-color:rgba(58, 14, 189, 0.76); color: #fff;" onclick="showData('total')">Total Requests: <?php echo $totalRequests; ?></button>
    </div>
</div>

<!-- Bootstrap for styling -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<div id="dataContent" class="mt-4"></div>

<style>
    th {
        background-color: rgb(39, 197, 165);
        color: #fff;
    }
    th, td {
        border: 1px solid #ccc;
    }
</style>

<script>
    function showData(type) {
        const dataContent = document.getElementById('dataContent');
        const requestTable = document.querySelector('table'); // Select the requests table
        
        requestTable.style.display = 'none'; // Hide the main requests table
        dataContent.style.display = 'block'; // Show the new data table
        dataContent.innerHTML = '<p>Loading data...</p>'; 

        fetch('fetch_requests_civil.php?type=' + type)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status !== 'success' || !Array.isArray(data.data)) {
                    throw new Error('Invalid data format received');
                }
                if (data.data.length === 0) {
                    dataContent.innerHTML = '<p>No data found for this category.</p>';
                    return;
                }

                let content = '<table class="table table-bordered table-striped">';
                content += '<thead><tr><th>Student Name</th><th>Enrollment Number</th><th>Branch</th><th>Year</th><th>Status</th><th>Date</th></tr></thead><tbody>';
                
                data.data.forEach(request => {
                    content += `
                        <tr>
                            <td>${request.student_name}</td>
                            <td>${request.enrollment_number}</td>
                            <td>${request.branch}</td>
                            <td>${request.year}</td>
                            <td>${request.status}</td>
                            <td>${request.date}</td>
                        </tr>`;
                });
                content += '</tbody></table>';
                dataContent.innerHTML = content;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                dataContent.innerHTML = `<p>Error loading data: ${error.message}. Please try again later.</p>`;
            });
    }

    function showApprovedRequests() {
        showData('approved'); 
    }
    function resetView() {
        const requestTable = document.querySelector('table');
        const dataContent = document.getElementById('dataContent');
        
        requestTable.style.display = 'none'; // Hide the main requests table
        dataContent.style.display = 'none'; // Hide the dynamic content
    }
    
</script>
<?php if (!empty($searchQuery) && !empty($student)): ?>
        <h3>Search Results:</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Enrollment Number</th>
                <th>Branch</th>
                <th>Year</th>
                <!--<th>Phone Number</th>-->
                <th>Email</th>
                <th>Admission Year</th>
                <th>Passout Year</th>
                <th>Status</th>
            </tr>
            <?php foreach ($student as $stud): ?>
                <tr>
                    <td><?= $stud['id']; ?></td>
                    <td><?= $stud['name']; ?></td>
                    <td><?= $stud['enrollment_number']; ?></td>
                    <td><?= $stud['branch']; ?></td>
                    <td><?= $stud['current_studying_year']; ?></td>
                    <!--<td><?= $stud['phone_number']; ?></td>-->
                    <td><?= $stud['email']; ?></td>
                    <td><?= $stud['admission_year']; ?></td>
                    <td><?= $stud['passout_year']; ?></td>
                    <td><?= $stud['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Enrollment Number</th>
            <!--<th>Phone</th>-->
            <th>Email</th>
            <th>Branch</th>
            <th>Year</th>
            <th>Date</th>
            <th>Application</th>
            <th>Action</th>
        </tr>
        <?php foreach ($requests as $request): ?>
            <tr id="row-<?= $request['id']; ?>">
                <td><?= $request['id']; ?></td>
                <td><?= $request['student_name']; ?></td>
                <td><?= $request['enrollment_number']; ?></td>
               <!-- <td><?= $request['phone']; ?></td>-->
                <td><?= $request['email']; ?></td>
                <td><?= $request['branch']; ?></td>
                <td><?= $request['year']; ?></td>
                <td><?= $request['date']; ?></td>
                <td>
                    <form action="view_application.php" method="POST">
                        <input type="hidden" name="request_id" value="<?= $request['id']; ?>">
                        <button type="submit" class="view-btn">View</button>
                    </form>
                </td>
                <td>
                    <!-- <button onclick="sendNotification('<?= $request['email']; ?>', '<?= $request['phone']; ?>', 'Approved', '<?= $request['id']; ?>')" class="approve-btn">Approve</button> -->
                    <button onclick="sendNotification('<?= $request['email']; ?>', 'Rejected', '<?= $request['id']; ?>')" class="reject-btn">Reject</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>


    
<script>
    function viewRequest(requestId) {
        window.location.href = 'view_request.php?request_id=' + requestId;
    }

    function signRequest(requestId) {
        window.location.href = 'generate_bonafide_certificate.php?request_id=' + requestId;
    }
</script>
</div>

<style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            width:100%;
            padding:0;
            background-color: #F5EDED;
        }
       
.search-bar {
    display: flex;
    right:20px;
    align-items: center;
}

.search-bar input {
    padding: 6px;
    width: 180px;
    border: 1px solid #9AA6B2;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.search-bar button {
    padding: 6px 12px;
    background: linear-gradient(135deg, rgb(89, 101, 231), rgb(38, 10, 80));
    color: #fff;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    margin-left: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
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
    background-color: rgb(217, 212, 234); /* Soft background color */
    color: black; /* Text color black */
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
    background-color: rgb(173, 187, 236); /* Light blue hover color */
    color: black; /* Ensure text stays black on hover */
}

/* Header styling */
.new-header {
    background-color: #fff;
    position: fixed;
    width: 100%;
    top: 10px;
    left: 0;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Outline effect */
.outline {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

/* Navigation bar */
.new-nav {
    position: absolute;
    top: 0px;
    left: 0;
    width: auto;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 8px;
}

/* Button container */
.new-container1 {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 10px;
}

/* Styled button */
.new-btn {
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
.new-btn:hover {
    background-color:rgb(129, 129, 240); /* Bright blue on hover */
}

        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            margin: 40px 50px 50px 50px;
            width: 100%;
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
            background-color:rgb(159, 135, 238);
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
            background-color:rgb(121, 117, 233);
        }

        .view-btn:hover {
            background-color:rgb(60, 60, 220);
        }
        .main > .inp {
            display: none;
        }

        .main {
            font-weight: 800;
            color: white;
            background-color:rgb(150, 181, 208);
            padding: 2px 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            height: 2rem;
            width: 8rem;
            position: absolute;
            top: 0;
            left: 1700px;
            cursor: pointer;
            justify-content: space-between;
        }

        .arrow {
            height: 34%;
            aspect-ratio: 1;
            margin-block: auto;
            position: relative;
            display: flex;
            justify-content: center;
            transition: all 0.3s;
        }

        .arrow::after,
        .arrow::before {
            content: "";
            position: absolute;
            background-color: white;
            height: 100%;
            width: 2.5px;
            border-radius: 500px;
            transform-origin: bottom;
        }

        .arrow::after {
            transform: rotate(35deg) translateX(-0.5px);
        }
        
        .arrow::before {
            transform: rotate(-35deg) translateX(0.5px);
        }

        .main > .inp:checked + .arrow {
            transform: rotateX(180deg);
        }

        .menu-container {
            background-color: white;
            color: black;
            border-radius: 10px;
            position: absolute;
            width: 100%;
            left: 0;
            top: 130%;
            overflow: hidden;
            clip-path: inset(0% 0% 0% 0% round 10px);
            transition: all 0.4s;
        }

        .menu-list {
            --delay: 0.4s;
            --trdelay: 0.15s;
            padding: 8px 10px;
            border-radius: inherit;
            transition: background-color 0.2s 0s;
            position: relative;
            transform: translateY(30px);
            opacity: 0;
        }

        .menu-list::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            height: 1px;
            background-color: rgba(0, 0, 0, 0.3);
            width: 95%;
        }

        .menu-list:hover {
            background-color: rgb(223, 223, 223);
        }

        .inp:checked ~ .menu-container {
            clip-path: inset(10% 50% 90% 50% round 10px);
        }

        .inp:not(:checked) ~ .menu-container .menu-list {
            transform: translateY(0);
            opacity: 1;
        }

        .bar-inp {
            -webkit-appearance: none;
            display: none;
            visibility: hidden;
        }

        .bar {
            display: flex;
            height: 50%;
            width: 20px;
            flex-direction: column;
            gap: 3px;
        }

        .bar-list {
            --transform: -25%;
            display: block;
            width: 100%;
            height: 3px;
            border-radius: 50px;
            background-color: white;
            transition: all 0.4s;
            position: relative;
        }

        .inp:not(:checked) ~ .bar > .top {
            transform-origin: top right;
            transform: translateY(var(--transform)) rotate(-45deg);
        }

        .inp:not(:checked) ~ .bar > .middle {
            transform: translateX(-50%);
            opacity: 0;
        }

        .inp:not(:checked) ~ .bar > .bottom {
            transform-origin: bottom right;
            transform: translateY(calc(var(--transform) * -1)) rotate(45deg);
        }
    </style>
</body>
</html>


