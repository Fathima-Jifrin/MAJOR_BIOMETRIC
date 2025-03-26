<?php
session_start();

require 'config.php' ;

if (isset($_POST['otp'], $_POST['voter_id'])) {
    $otp = $_POST['otp'];
    $voter_id = $_POST['voter_id'];

    $query = "SELECT * FROM voter_registration WHERE id = ? AND otp = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $voter_id, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $updateQuery = "UPDATE voter_registration SET ev = 'yes' WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("s", $voter_id);
        if ($updateStmt->execute()) {
            echo "<script>alert('Email verified successfully.'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error updating verification status.'); window.location.href='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.'); window.location.href='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('OTP or voter ID missing.'); window.location.href='dashboard.php';</script>";
}
?>
