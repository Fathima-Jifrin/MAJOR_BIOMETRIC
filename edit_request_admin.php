<?php
session_start();

require 'config.php';

if (isset($_POST['approve']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    
    $query = "SELECT aadhaar_number, new_constituency, new_address FROM edit_request WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($aadhaar_number, $new_constituency, $new_address);
    $stmt->fetch();
    $stmt->close();

    $update_query = "UPDATE voter_id SET constituency = ?, address = ? WHERE aadhaar_number = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("sss", $new_constituency, $new_address, $aadhaar_number);

    if ($stmt_update->execute()) {
        
        $status_update_query = "UPDATE edit_request SET status = 'approved' WHERE id = ?";
        $stmt_status_update = $conn->prepare($status_update_query);
        $stmt_status_update->bind_param("i", $request_id);
        $stmt_status_update->execute();
        $stmt_status_update->close();

        $success_message = "Edit request approved and voter details updated successfully.";
    } else {
        $error_message = "Error: Could not approve the request. Please try again later.";
    }

    $stmt_update->close();
}


if (isset($_POST['reject']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];


    $query = "SELECT vi.email, er.aadhaar_number, er.new_constituency, er.new_address 
              FROM edit_request er
              JOIN voter_registration vi ON er.aadhaar_number = vi.aadhaar_number
              WHERE er.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($voter_email, $aadhaar_number, $new_constituency, $new_address);
    $stmt->fetch();
    $stmt->close();

    $rejection_message = "Dear Voter,<br><br>We regret to inform you that your voter details update request has been rejected due to one the following reasons:<br>";
    $rejection_message .= "<ul>
                              <li>Invalid documents submitted.</li>
                              <li>Mismatch in the Aadhaar details.</li>
                              <li>Incomplete request information.</li>
                            </ul>";
    $rejection_message .= "<br>Please contact the Election Officer for further assistance.<br><br>Thank you.";


    sendRejectionEmail($voter_email, $rejection_message);

    $status_update_query = "UPDATE edit_request SET status = 'rejected' WHERE id = ?";
    $stmt_status_update = $conn->prepare($status_update_query);
    $stmt_status_update->bind_param("i", $request_id);

    if ($stmt_status_update->execute()) {
        $success_message = "Edit request has been rejected, and the voter has been notified.";
    } else {
        $error_message = "Error: Could not reject the request. Please try again later.";
    }

    $stmt_status_update->close();
}


function sendRejectionEmail($email, $message) {
    include_once('class.phpmailer.php');
    include_once('class.smtp.php');

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ebinbenny1709@gmail.com'; 
        $mail->Password = 'kouiproacwnesmpg';          
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ebinbenny1709@gmail.com', 'Election Commission of India');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Voter Update Request Rejected';
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        echo 'Error: ' . $mail->ErrorInfo;
    }
}



$query = "SELECT er.id, er.aadhaar_number, er.new_constituency, er.new_address, er.status, er.proof, vi.full_name, vi.constituency, vi.current_address 
            FROM edit_request er
            JOIN voter_id vi ON er.aadhaar_number = vi.aadhaar_number
            WHERE er.status = 'processing'";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Requests</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

      
        .header-container {
            background-color: darkblue;
            color: white;
            padding: 20px;
            text-align: center;
        }

     
        .back-button-container {
            margin: 15px;
        }

        .back-button {
            text-decoration: none;
            padding: 10px 15px;
            background-color: #2980b9;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #1a5276;
        }

       
        .table-container {
            width: 90%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

       
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .requests-table th, .requests-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .requests-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .requests-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .requests-table td a {
            color: #1a73e8;
            text-decoration: none;
        }

        .requests-table td a:hover {
            text-decoration: underline;
        }

        
        .approve-btn {
            background-color: #27ae60;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .approve-btn:hover {
            background-color: #1e8449;
        }

        
        .reject-btn {
            background-color: red;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .reject-btn:hover {
            background-color: #c0392b;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

       
        footer {
            text-align: center;
            padding: 10px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1>Election Commission - Admin Panel</h1>
    </div>
</header>

<div class="back-button-container">
    <a href="edit_voter.php" class="back-button">&larr; Back</a>
</div>

<main>
    <div class="table-container">
        <h2>Pending Edit Requests</h2>
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?= $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= $error_message; ?></p>
        <?php endif; ?>
        
        <table class="requests-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Aadhaar Number</th>
                    <th>Current Constituency</th>
                    <th>New Constituency</th>
                    <th>New Address</th>
                    <th>Proof</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                   if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['full_name']}</td>
                <td>{$row['aadhaar_number']}</td>
                <td>{$row['constituency']}</td>
                <td>{$row['new_constituency']}</td>
                <td>{$row['new_address']}</td>
                <td><a href='uploads/edit_requests/{$row['proof']}' target='_blank'>View Proof</a></td>
                <td>
                    <form method='POST' action=''>
                        <input type='hidden' name='request_id' value='{$row['id']}'>
                        <button type='submit' name='approve' class='approve-btn'>Approve</button>
                        <button type='submit' name='reject' class='reject-btn'>Reject</button>
                    </form>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No pending requests</td></tr>";
}
?>
                
            </tbody>
        </table>
    </div>
</main>

<footer>
    <p>&copy; 2025 Election Commission. All rights reserved.</p>
</footer>

</body>
</html>

<?php $conn->close(); ?>
