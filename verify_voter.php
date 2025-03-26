<?php
session_start();

require 'config.php' ;

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access this page.'); window.location.href='adminlog.html';</script>";
    exit();
}
  $aadhaar_number=  $_SESSION['aadhaar_number'];


$error_message = '';
$polling_booth = '';
$relation_status = '';
$mismatch_reason = '';
$parent_row = [];


$epic_logo = 'logo.png'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $parent_voter_id = $_POST['voter_id'] ?? '';

    if ($action === 'approve') {
        
        $aadhaar_number = $_SESSION['aadhaar_number'] ?? '';
        $parent_sql = "SELECT * FROM voter_id WHERE EPIC = '$parent_voter_id'";
        $parent_result = $conn->query($parent_sql);

        if ($parent_result->num_rows > 0) {
            $parent_row = $parent_result->fetch_assoc();
            $polling_booth = $parent_row['Booth'];

            $update_sql = "UPDATE voter_id SET Booth = '$polling_booth' WHERE aadhaar_number = '$aadhaar_number'";
            if ($conn->query($update_sql)) {
                $relation_status = "Polling booth assigned successfully.";
            } else {
                $relation_status = "Error assigning polling booth. Please try again.";
            }
        } else {
            $error_message = "Parent voter details not found.";
        }
    } elseif ($action === 'reject') {
        
        $reject_reason = "Mismatch in parent EPIC card with the given data.";
        $aadhaar_number = $_SESSION['aadhaar_number'] ?? '';

        $update_sql = "UPDATE voter_registration SET rejectreason = '$reject_reason' WHERE aadhaar_number = '$aadhaar_number'";
        if ($conn->query($update_sql)) {
            echo "<script>alert('Verification rejected. Redirecting to verification page.'); window.location.href='verify_application.php?aadhaar_number=$aadhaar_number';</script>";
            exit();
        } else {
            $relation_status = "Error rejecting verification. Please try again.";
        }
    }
} elseif (isset($_GET['voter_id'])) {
    $parent_voter_id = $_GET['voter_id'];

    
    $parent_sql = "SELECT * FROM voter_id WHERE EPIC = '$parent_voter_id'";
    $parent_result = $conn->query($parent_sql);

    if ($parent_result->num_rows > 0) {
        $parent_row = $parent_result->fetch_assoc();
    } else {
        $error_message = "No details found for the given parent voter ID.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Voter Details</title>
    <style>
        
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify Voter Details</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center;">
                <?php echo htmlspecialchars($error_message); ?>
            </p>
        <?php else: ?>
            <div class="voter-id-card">
                <h3>Parent's Voter ID Card</h3>
                <div class="logo">
                    <img src="<?php echo htmlspecialchars($epic_logo); ?>" alt="EPIC Logo">
                </div>
                <div class="details">
                    <p><strong>EPIC Number:</strong> <?php echo htmlspecialchars($parent_row['EPIC'] ?? 'N/A'); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($parent_row['full_name'] ?? 'N/A'); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($parent_row['gender'] ?? 'N/A'); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($parent_row['age'] ?? 'N/A'); ?></p>
                    <p><strong>Polling Booth:</strong> <?php echo htmlspecialchars($parent_row['Booth'] ?? 'N/A'); ?></p>
                </div>
            </div>
            <form method="POST">
                <input type="hidden" name="voter_id" value="<?php echo htmlspecialchars($parent_voter_id); ?>">
                <div class="buttons">
                    <button class="button approve" name="action" value="approve">Approve</button>
                    <button class="button reject" name="action" value="reject">Reject</button>
                </div>
            </form>
            <div class="detail">
                <div class="label">Status:</div>
                <div class="value">
                    <?php echo htmlspecialchars($relation_status); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="footer">
        &copy; 2024 Election Commission. All Rights Reserved.
    </div>
</body>
</html>


    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .detail {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #000;
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #fff;
            margin: 5px;
        }
        .approve {
            background-color: #28a745;
        }
        .approve:hover {
            background-color: #218838;
        }
        .reject {
            background-color: #dc3545;
        }
        .reject:hover {
            background-color: #c82333;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background: #001f5b;
            color: #fff;
            margin-top: 20px;
            border-radius: 0 0 8px 8px;
        }
        .voter-id-card {
            border: 2px solid #333;
            border-radius: 8px;
            padding: 20px;
            background: #f9f9f9;
            margin: 20px 0;
            text-align: center;
        }
        .voter-id-card .logo img {
            width: 50px;
            margin-bottom: 10px;
        }
        .voter-id-card .details p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
