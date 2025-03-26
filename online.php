<?php
session_start();


if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in as admin to access the dashboard.'); window.location.href='admin_login.php';</script>";
    exit();
}


require 'config.php' ;


$query_approved = "SELECT COUNT(*) AS approved_count FROM online_voting_requests WHERE status = 'approved'";
$query_rejected = "SELECT COUNT(*) AS rejected_count FROM online_voting_requests WHERE status = 'rejected'";
$query_pending = "SELECT COUNT(*) AS pending_count FROM online_voting_requests WHERE status = 'submitted'";

$approved_result = $conn->query($query_approved);
$rejected_result = $conn->query($query_rejected);
$pending_result = $conn->query($query_pending);

$approved_count = $approved_result->fetch_assoc()['approved_count'];
$rejected_count = $rejected_result->fetch_assoc()['rejected_count'];
$pending_count = $pending_result->fetch_assoc()['pending_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Voting</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
   body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: darkblue;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            height: 100px;
        }

        .back-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            margin: 20px;
            display: block;
            width: fit-content;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }

        .vote-summary {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        .vote-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .vote-card h3 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .vote-card p {
            font-size: 1.5rem;
            color: #007bff;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .nav-item {
            background-color: #007bff;
            color: white;
            padding: 15px 25px;
            width: 220px;
            text-align: center;
            border-radius: 8px;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .nav-item:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .nav-item a {
            color: white;
            text-decoration: none;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 0.9rem;
        }

        footer a {
            color: #007bff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header>

    <img src="logo.png" alt="Logo" class="logo">
    <h3>Election Commission of India</h3>

    Admin Dashboard - Online Voting Management
</header>
<button class="back-button" onclick="window.location.href='returningdash.php'">
    <i class="fas fa-arrow-left"></i> Back
</button>

<div class="container">
    <div class="vote-summary">
        
        <div class="vote-card">
            <h3>Total Approved Request</h3>
            <p><?php echo $approved_count; ?></p>
        </div>
        <div class="vote-card">
            <h3>Total Rejected Request</h3>
            <p><?php echo $rejected_count; ?></p>
        </div>
        <div class="vote-card">
            <h3>Total Pending Request</h3>
            <p><?php echo $pending_count; ?></p>
        </div>
    </div>

    <div class="navigation">
        
        <div class="nav-item">
            <a href="verifyonlinereq.php"><i class="fas fa-cogs"></i> Process New Requests</a>
        </div>
        <div class="nav-item">
            <a href="view_ele_list.php"><i class="fas fa-check-circle"></i> View Approved Voters</a>
        </div>
        <div class="nav-item">
            <a href="voting/conduct_online.php"><i class="fas fa-vote-yea"></i> Conduct Online Voting</a>
        </div>
        <div class="nav-item">
            <a href="security_protocol.php"><i class="fas fa-shield-alt"></i> Implement Security Protocols</a>
        </div>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Election Commission of India. All rights reserved. | <a href="logout.php">Logout</a>
</footer>

</body>
</html>
