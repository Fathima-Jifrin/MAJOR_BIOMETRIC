<?php
session_start();



require 'config.php';

if (isset($_POST['approve']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    
    $query = "SELECT aadhaar_number FROM delete_request WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($aadhaar_number);
    $stmt->fetch();
    $stmt->close();

    
    $update_query = "UPDATE delete_request SET status = 'approved' WHERE id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("i", $request_id);

    if ($stmt_update->execute()) {
        
        $delete_voter_id_query = "DELETE FROM voter_id WHERE aadhaar_number = ?";
        $stmt_delete_voter_id = $conn->prepare($delete_voter_id_query);
        $stmt_delete_voter_id->bind_param("s", $aadhaar_number);
        $stmt_delete_voter_id->execute();

        $delete_voter_registration_query = "DELETE FROM voter_registration WHERE aadhaar_number = ?";
        $stmt_delete_voter_registration = $conn->prepare($delete_voter_registration_query);
        $stmt_delete_voter_registration->bind_param("s", $aadhaar_number);
        $stmt_delete_voter_registration->execute();

        
        $success_message = "Deletion request approved and user removed from the system successfully.";
    } else {
        $error_message = "Error: Could not approve the request. Please try again later.";
    }
    
    $stmt_update->close();
}


if (isset($_POST['reject']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];


    $query = "SELECT vi.email, dr.aadhaar_number, dr.reason 
              FROM delete_request dr
              JOIN voter_registration vi ON dr.aadhaar_number = vi.aadhaar_number
              WHERE dr.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($voter_email, $aadhaar_number, $reason);
    $stmt->fetch();
    $stmt->close();

    $rejection_message = "Dear Voter,<br><br>We regret to inform you that your deletion request has been rejected due to the following reason(s):<br>";
    $rejection_message .= "<ul>
                              <li>Invalid documents submitted.</li>
                              <li>Mismatch in the Aadhaar details.</li>
                              <li>Incomplete request information.</li>
                            </ul>";
    $rejection_message .= "<br>Please contact the Election Officer for further assistance.<br><br>Thank you.";

    
    sendRejectionEmail($voter_email, $rejection_message);

    
    $status_update_query = "UPDATE delete_request SET status = 'rejected' WHERE id = ?";
    $stmt_status_update = $conn->prepare($status_update_query);
    $stmt_status_update->bind_param("i", $request_id);

    if ($stmt_status_update->execute()) {
        $success_message = "Deletion request has been rejected, and the voter has been notified.";
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
        $mail->Username = 'your-email@gmail.com';  
        $mail->Password = 'your-email-password'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Election Commission of India');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Voter Deletion Request Rejected';
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        echo 'Error: ' . $mail->ErrorInfo;
    }
}




$query = "SELECT dr.id, dr.voter_id, dr.aadhaar_number, dr.reason, dr.proof, dr.status, vi.full_name, vi.EPIC
          FROM delete_request dr
          JOIN voter_id vi ON dr.aadhaar_number = vi.aadhaar_number
          WHERE dr.status = 'processing'";

$result = $conn->query($query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Election Commission Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    
    <header>
        <div class="header-container">
            <div class="logo">
        <img src="logo.png" alt="ECI Logo" width="100" height="100">
    </div>
            <h1>Election Commission - Admin Panel</h1>
            
            </nav>
        </div>
    </header>
    <div class="back-button-container">
            <a href="delete_voter.php" class="back-button">← Back</a>
        </div>
    
    <main>
        <div class="table-container">
            <h2>Pending Deletion Requests</h2>
            <?php if (isset($success_message)): ?>
                <p class="success-message"><?= $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?= $error_message; ?></p>
            <?php endif; ?>
            
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Voter ID</th>
                        <th>Name</th>
                        <th>Aadhaar Number</th>
                        <th>EPIC</th>
                        <th>Reason</th>
                        <th>Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
         <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['voter_id']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['aadhaar_number']}</td>
                <td>{$row['EPIC']}</td>
                <td>{$row['reason']}</td>
                <td><a href='uploads/delete_requests/{$row['proof']}' target='_blank'>View Proof</a></td>
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

<?php
$conn->close();
?>
<style>

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

header {
    background-color: darkblue;
    color: #fff;
    padding: 20px 0;
    text-align: center;
}

header h1 {
    margin: 0;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

nav ul li {
    display: inline;
    margin: 0 15px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
}

main {
    padding: 20px;
}

.table-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table-container h2 {
    margin-top: 0;
    text-align: center;
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
    background-color: #e74c3c; 
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
    font-size: 16px;
    margin: 20px 0;
    text-align: center;
}

.error-message {
    color: red;
    font-size: 16px;
    margin: 20px 0;
    text-align: center;
}


footer {
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 10px;
    position: relative;
    bottom: 0;
    width: 100%;
    margin-top:27%;
}

footer p {
    margin: 0;
}


button {
    cursor: pointer;
    padding: 10px 15px;
    border-radius: 5px;
    border: none;
    background-color: #4CAF50;
    color: white;
    font-size: 14px;
}

button:hover {
    background-color: #45a049;
}

button:focus {
    outline: none;
}
.back-button-container {
    margin-top: 20px;
    margin-left: 10px; 
}

.back-button {
    display: inline-block;
    padding: 10px 15px;
    background-color: #0056b3;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
}

.back-button:hover {
    background-color: #004494;
}

</style>