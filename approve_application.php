<?php
session_start(); 

require 'config.php';


if (!isset($_SESSION['aadhaar_number'])) {
    echo "Aadhaar number is not set in session.";
    exit;
}

$aadhaarNumber = $_SESSION['aadhaar_number'];


$sqlVoter = "SELECT full_name, aadhaar_number, email, rejectreason FROM voter_registration WHERE aadhaar_number = ?";
$stmtVoter = $conn->prepare($sqlVoter);
$stmtVoter->bind_param("s", $aadhaarNumber);
$stmtVoter->execute();
$resultVoter = $stmtVoter->get_result();

if ($resultVoter->num_rows > 0) {
    $row = $resultVoter->fetch_assoc();
    $full_name = $row['full_name'];
    $email = $row['email'];

    if ($row['rejectreason'] === 'nil') {
        
        function generateEPIC() {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            return substr(str_shuffle($characters), 0, 8);
        }

        $epic = generateEPIC();
        
        
        $updateEPIC = "UPDATE voter_id SET EPIC = ? WHERE aadhaar_number = ?";
        $stmtUpdateEPIC = $conn->prepare($updateEPIC);
        $stmtUpdateEPIC->bind_param("ss", $epic, $aadhaarNumber);
        $stmtUpdateEPIC->execute();
        $stmtUpdateEPIC->close();

        
        $updateVoterRegistration = "UPDATE voter_registration SET astatus = 'approved' WHERE aadhaar_number = ?";
        $stmtUpdate = $conn->prepare($updateVoterRegistration);
        $stmtUpdate->bind_param("s", $aadhaarNumber);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        echo "Application Approved. Voter ID: " . $epic;

        
        require_once("class.phpmailer.php");
        require_once("class.smtp.php");

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
            $mail->addAddress($email, $full_name);
            $mail->isHTML(true);

            $mail->Subject = 'Voter Application Approved by Election Commission of India';
            $mail->Body = 'Dear ' . htmlspecialchars($full_name) . ',<br><br>' . 
                          'We are pleased to inform you that your voter application has been approved.<br><br>' . 
                          'Your Voter ID is: <b>' . htmlspecialchars($epic) . '</b><br><br>' . 
                          '<a href="https://biometric.free.nf/voter_id.php?voter_id=' . htmlspecialchars($epic) . '" 
                          style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Download e-Voter Card</a><br><br>' .
                          'Thank you for registering!<br><br>' .
                          'Sincerely,<br>' .
                          'Election Commission of India';

            $mail->send();
            echo "Email Sent Successfully.";
        } catch (Exception $e) {
            echo "<script>alert('Email could not be sent. Error: " . $mail->ErrorInfo . "'); window.location.href='av.php';</script>";
        }
    } else {
        echo "Can't approve. Reject reason is not nil.";
    }
} else {
    echo "No application found for the provided Aadhaar number.";
}


$conn->close();
?>
