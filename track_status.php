<?php
require 'config.php' ;

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    $sql = "SELECT * FROM voter_id WHERE application_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
$row = $result->fetch_assoc();
$aadhaar_number = $row['aadhaar_number'];


 $sql1 = "SELECT * FROM voter_registration WHERE aadhaar_number = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $aadhaar_number);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
$row1 = $result1->fetch_assoc();
$status = $row1['astatus'];

    
    echo json_encode(['status' => $status]);
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Application ID not provided']);
}

$conn->close();
?>
