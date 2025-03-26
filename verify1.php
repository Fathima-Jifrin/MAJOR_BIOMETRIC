<?php
session_start();  

require 'config.php' ;

$aadhaar_number = $_SESSION['aadhaar_number'] ?? '';
$captured_fingerprint = $_SESSION['captured_fingerprint'] ?? '';

if (!$aadhaar_number || !$captured_fingerprint) {
    die("Invalid session data.");
}


$sql = "SELECT index_f FROM aadhaar_data WHERE aadhaar_number = '$aadhaar_number'";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("Aadhaar number not found.");
}
$row = $result->fetch_assoc();
$stored_fingerprint = $row['index_f'];


$apiUrl = "https://fingerprintapi.mxface.ai/api/FingerPrint/Verify";
$subscriptionKey = "PsCyA5gs9MnwPyHWoc-DveqDty71y3480";

$data = [
    "fingerPrint1" => $stored_fingerprint,
    "fingerPrint2" => $captured_fingerprint
];

$headers = [
    "Content-Type: application/json",
    "Subscriptionkey: $subscriptionKey"
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData["matched"] === 1) {
        
        header("Location: biometric_successful.php");
        exit(); 
    } else {
  
        header("Location: cap.php");
        exit(); 
    }
}

?>