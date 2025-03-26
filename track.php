<?php
session_start();

require 'config.php' ;

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];
    
    $sql = "SELECT * FROM voter_id WHERE application_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $aadhaar_number = $row['aadhaar_number'];

    
    $sql1 = "SELECT astatus, rejectreason, errortype FROM voter_registration WHERE aadhaar_number = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $aadhaar_number);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $row1 = $result1->fetch_assoc();
    $status = $row1['astatus'] ?? 'Unknown';
    $reject_reason = $row1['reject_reason'] ?? '';
    $error_type = $row1['error_type'] ?? '';

    $stmt->close();
    $stmt1->close();
} else {
    die("No application ID provided.");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Voter Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #1a237e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .logo {
            width: 50px;
            vertical-align: middle;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .progress-bar-container {
            position: relative;
            width: 100%;
            background-color: #e9ecef;
            border-radius: 5px;
            height: 30px;
            margin: 20px 0;
        }
        .progress-bar-fill {
            background-color: #007bff;
            height: 100%;
            width: 0%;
            border-radius: 5px;
            transition: width 0.5s ease-in-out;
        }
        .milestone {
            position: absolute;
            top: -25px;
            color: grey;
            font-weight: bold;
            transform: translateX(-50%);
            text-align: center;
        }
        .milestone.complete { color: green; }
        .milestone1 { left: 10%; }
        .milestone2 { left: 50%; }
        .milestone3 { left: 90%; }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .btn:hover { background-color: #0056b3; }
        .rejection-reason { color: red; font-weight: bold; margin-top: 10px; }
        .reapply-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8d7da;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        .reapply-btn {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .reapply-btn:hover { background-color: #218838; }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <img src="logo.png" alt="Election Commission Logo" class="logo">
        <h1>Election Commission of India - Voter Dashboard</h1>
    </header>

    <div class="container">
        <h2>Track Your Voter Application</h2>
        <p>Your application ID is: <strong><?php echo htmlspecialchars($application_id); ?></strong></p>

        <div class="progress-bar-container">
            <div id="progressBarFill" class="progress-bar-fill"></div>

            <div class="milestone milestone1 <?php echo ($status == 'submitted') ? 'complete' : ''; ?>">
                <p>1. Submitted</p>
            </div>
            <div class="milestone milestone2 <?php echo ($status == 'Under Review') ? 'complete' : ''; ?>">
                <p>2. Under Review</p>
            </div>
            <div class="milestone milestone3 <?php echo ($status == 'approved') ? 'complete' : ''; ?>">
                <p>3. Approved</p>
            </div>
        </div>

        <?php if ($status == 'rejected'): ?>
            <div class="rejection-reason">
                <p><strong>Application Rejected</strong></p>
                <p>Reason: <?php echo htmlspecialchars($reject_reason); ?></p>
                <p>Error Type: <?php echo htmlspecialchars($error_type); ?></p>
            </div>

            
            <div class="reapply-section">
                <p>If you wish to reapply, please click the button below to submit a new application form.</p>
                <a href="new_application_form.php" class="reapply-btn">Reapply</a>
            </div>
        <?php endif; ?>

        <a href="dashboard.php" class="btn">Back to Home</a>
    </div>

    <script>
        const status = "<?php echo $status; ?>";
        let progress = 0;
        if (status === "submitted") {
            progress = 33;
        } else if (status === "Under Review") {
            progress = 66;
        } else if (status === "Approved") {
            progress = 100;
        } else if (status === "Rejected") {
            progress = 0; 
        }
        document.getElementById('progressBarFill').style.width = progress + '%';
    </script>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
