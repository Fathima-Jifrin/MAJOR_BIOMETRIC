<?php
session_start(); 

require 'config.php' ;

if (!isset($_SESSION['aadhaar_number'])) {
    echo "Aadhaar number is not set in session.";
    exit;
}

$aadhaarNumber = $_SESSION['aadhaar_number']; 


$rejectReason = isset($_POST['rejectReason']) ? $_POST['rejectReason'] : '';
$errorType = isset($_POST['errorType']) ? $_POST['errorType'] : '';


$sql = "UPDATE voter_registration 
        SET astatus = 'rejected', rejectreason = '$rejectReason', errortype = '$errorType'
        WHERE aadhaar_number = '$aadhaarNumber'";

if ($conn->query($sql) === TRUE) {
echo "<script>
        alert('Application Rejected. Reason: " . htmlspecialchars($rejectReason) . "');
        window.location.href = 'av.php';
      </script>";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
