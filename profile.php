<?php
session_start();


if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Please log in to access your profile.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id']; 


require 'config.php' ;


$stmt = $conn->prepare("SELECT full_name, aadhaar_number, phone_number, date_of_birth, email, registration_date FROM voter_registration WHERE id = ?");
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Election Commission of India</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        header {
            background-color: darkblue;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .logo {
            width: 50px; 
            vertical-align: middle;
        }

        .nav {
            position: absolute;
            right: 20px;
            top: 15px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .nav a:hover {
            background-color: #FF9933; 
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px; 
        }

        h1, h2 {
            margin: 0;
            text-align: center;
        }

        .profile-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .profile-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .profile-table td:first-child {
            font-weight: bold;
            text-align: right;
            width: 30%;
        }

        .profile-table td:last-child {
            text-align: left;
        }

        .contact {
            margin-top: 30px;
            font-size: 14px;
            color: #555;
            text-align: center;
        }

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
        <h1>User Profile</h1>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Your Profile Details</h2>
     
        <table class="profile-table">
            <tr>
                <td>Name:</td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
            </tr>
            <tr>
                <td>Aadhaar Number:</td>
                <td><?php echo htmlspecialchars(substr($user['aadhaar_number'], 0, -4) . '****'); ?></td>
            </tr>
            <tr>
                <td>Phone Number:</td>
                <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
            </tr>
            <tr>
                <td>Date of Birth:</td>
                <td><?php echo htmlspecialchars($user['date_of_birth']); ?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <td>Registration Date:</td>
                <td><?php echo htmlspecialchars($user['registration_date']); ?></td>
            </tr>
        </table>

        <div class="contact">
            <p>If you have any questions, please contact our support team.</p>
            <p>Email: support@eci.gov.in | Phone: 1800 111 111</p>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
