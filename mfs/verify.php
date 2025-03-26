<?php
require 'config.php';
// Fetch Aadhaar users from database
$sql = "SELECT id, full_name, aadhaar_number FROM aadhaar_data";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Check if an Aadhaar number is selected
$aadhaar_number = $_GET['aadhaar'] ?? '';
$verification_result = '';

if ($aadhaar_number) {
    // Fetch stored fingerprint
    $sql = "SELECT index_f FROM aadhaar_data WHERE aadhaar_number = '$aadhaar_number'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        die("Aadhaar number not found.");
    }
    $row = $result->fetch_assoc();
    $stored_fingerprint = $row['index_f'];

    // MFScan API for capturing fingerprint
require 'link.php';
    function postMFScanClient($method, $data = null) {
        global $uri;
        $url = $uri . $method;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'error' => $error];
        }

        curl_close($ch);
        return ['success' => $httpCode === 200, 'data' => json_decode($response, true)];
    }

    // Capture fingerprint
    $response = postMFScanClient("capture", ["Quality" => 80, "TimeOut" => 10000]);

    if (!$response['success'] || !isset($response['data']['BitmapData'])) {
        $verification_result = "Error: " . ($response['data']['ErrorDescription'] ?? 'Unknown error');
    } else {
        $captured_fingerprint = $response['data']['BitmapData'];

        // Verify fingerprints using FingerprintAPI
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

                $verification_result = "✅ Fingerprints match!";
            } else {
                $verification_result = "❌ Fingerprints do not match.";
            }
        } else {
            $verification_result = "Error: HTTP Status Code $httpCode";
        }

        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aadhaar Fingerprint Verification</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #111; color: white; }
        table { width: 80%; margin: auto; border-collapse: collapse; background: #222; }
        th, td { border: 1px solid #555; padding: 10px; text-align: left; color: white; }
        th { background-color: #4CAF50; }
        button { padding: 8px 15px; background: blue; color: white; border: none; cursor: pointer; }
        button:hover { background: darkblue; }
        .result { font-size: 1.2em; margin-top: 20px; }
    </style>
</head>
<body>

    <h2>Select a User to Capture Fingerprint</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Aadhaar Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['full_name'] ?></td>
                    <td><?= $user['aadhaar_number'] ?></td>
                    <td><a href="?aadhaar=<?= $user['aadhaar_number'] ?>"><button>Scan & Verify</button></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($aadhaar_number): ?>
        <div class="result">
            <h3>Verification Result for Aadhaar: <?= $aadhaar_number ?></h3>
            <p><?= $verification_result ?></p>
            <?php

 echo '<audio controls>
            <source src="1740721319819xx1xc2to-voicemaker.in-speech.mp3" type="audio/mp3">
            Your browser does not support the audio element.
          </audio>';
            ?>
        </div>
    <?php endif; ?>

</body>
</html>
