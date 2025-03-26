<?php
session_start();

require 'config.php' ;


if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in as admin to access the dashboard.'); window.location.href='admin_login.php';</script>";
    exit();
}


$query = "
    SELECT ovr.id, ovr.name, ovr.aadhaar_number, ovr.reason, ovr.proof_file_path, v.EPIC
    FROM online_voting_requests ovr
    LEFT JOIN voter_id v ON ovr.aadhaar_number = v.aadhaar_number
    WHERE ovr.eligibility_status = 'yes' AND ovr.status='submitted' AND ovr.rejection_reason IS NULL
";
$result = $conn->query($query);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_request'])) {
        
        $request_id = $_POST['request_id'];
        $update_query = "UPDATE online_voting_requests SET status = 'approved' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $request_id);
        if ($stmt->execute()) {
            echo "<script>alert('Request approved successfully.'); window.location.href = 'verifyonlinereq.php';</script>";
        } else {
            echo "<script>alert('Error approving the request. Please try again.');</script>";
        }
    }

    if (isset($_POST['reject_request'])) {
        
        $request_id = $_POST['request_id'];
        $rejection_reason = $_POST['rejection_reason'];
        $update_query = "UPDATE online_voting_requests SET status = 'rejected', rejection_reason = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $rejection_reason, $request_id);
        if ($stmt->execute()) {
            echo "<script>alert('Request rejected successfully.'); window.location.href = 'verifyonlinereq.php';</script>";
        } else {
            echo "<script>alert('Error rejecting the request. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Process Online Voting Requests</title>
    <style>
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        header {
            background-color:darkblue;
            color: white;
            text-align: center;
            padding: 20px;
            border-bottom: 5px solid #003366;
        }

        header .logo img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 2.2em;
            font-weight: 600;
            margin-top: 10px;
        }

       
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

       
        table {
            width: 100%;
          border:1px;
            margin-top: 20px;
            border-color:black;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 1.1em;
        }

        th {
            background-color: darkblue;
            color: white;
            font-size: 1.1em;
        }

        tr:hover {
            background-color: #f7f9fc;
        }

        
        button {
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn:hover {
            background-color: #c82333;
        }

        
        .reject-reason {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
            min-height: 80px;
            resize: vertical;
        }

        .reject-reason:focus {
            border-color: #007bff;
        }

        
        .proof-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .proof-link:hover {
            text-decoration: underline;
        }

        
        .error {
            color: red;
            font-size: 0.9em;
        }

        
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            table {
                font-size: 0.9em;
            }

            button {
                padding: 8px 16px;
            }

            h1 {
                font-size: 1.8em;
            }
        }
        footer {
    text-align: center;
    padding: 20px 0;
    background-color: #333;
    color: white;
    position: fixed;
    bottom: 0;
    width: 100%;
}

    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="logo.png" alt="logo">
    </div>
    <h1>Admin - Process Online Voting Requests</h1>
</header>

<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Voter Name</th>
                    <th>Aadhaar Number</th>
                    <th>Reason</th>
                    <th>EPIC Number</th>
                    <th>Uploaded Proof</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['aadhaar_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['EPIC'] ?? 'Not Available'); ?></td>
                        <td>
                            <?php if (!empty($row['proof_file_path'])): ?>
                                
                                <a href="<?php echo htmlspecialchars($row['proof_file_path']); ?>" target="_blank" class="proof-link">
                                    View Proof
                                </a>
                            <?php else: ?>
                                No proof uploaded
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="approve_request" class="approve-btn">Approve</button>
                                <button type="submit" name="reject_request" class="reject-btn">Reject</button>
                                <textarea name="rejection_reason" class="reject-reason" placeholder="Reason for rejection (optional)"></textarea>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending requests found.</p>
    <?php endif; ?>
</div>
  <footer>
    <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
  </footer>
</body>
</html>
