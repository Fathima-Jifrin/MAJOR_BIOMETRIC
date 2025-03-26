<?php
session_start();
require 'config.php';


if (isset($_POST['aadhaar_number'])) {
    $aadhaar_number = $_POST['aadhaar_number'];

    
    $delete_voter_id = "DELETE FROM voter_id WHERE aadhaar_number = ?";
    $stmt1 = $conn->prepare($delete_voter_id);
    $stmt1->bind_param("s", $aadhaar_number);
    $stmt1->execute();
    
  
    $delete_voter_registration = "DELETE FROM voter_registration WHERE aadhaar_number = ?";
    $stmt2 = $conn->prepare($delete_voter_registration);
    $stmt2->bind_param("s", $aadhaar_number);
    $stmt2->execute();

    
    if ($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0) {
        echo "<script>alert('Voter deleted successfully!'); window.location.href='delete_voter.php';</script>";
    } else {
        echo "<script>alert('Error: Voter not found or already deleted.'); window.location.href='delete_voter.php';</script>";
    }

   
    $stmt1->close();
    $stmt2->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='delete_voter.php';</script>";
}

$conn->close();
?>
