<?php
session_start();

require 'config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $aadhaar_number = $_POST['aadhaar_number'];  
    $constituency = $_POST['constituency']; 
    $current_address = $_POST['current_address'];  
    $permanent_address = $_POST['permanent_address'];  

    
    $aadhaar_number = $conn->real_escape_string($aadhaar_number);
    $constituency = $conn->real_escape_string($constituency);
    $current_address = $conn->real_escape_string($current_address);
    $permanent_address = $conn->real_escape_string($permanent_address);

    $sql = "UPDATE voter_id 
            SET constituency = ?, current_address = ?, permanent_address = ? 
            WHERE aadhaar_number = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $constituency, $current_address, $permanent_address, $aadhaar_number);

        if ($stmt->execute()) {
            echo "<script>alert('Voter details updated successfully.'); window.location.href='edit_voter.php';</script>";
        } else {
            echo "<script>alert('Error updating details. Please try again.'); window.location.href='edit_voter.php';</script>";
        }
        
        
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing the statement.'); window.location.href='edit_voter.php';</script>";
    }
}

$conn->close();
?>
