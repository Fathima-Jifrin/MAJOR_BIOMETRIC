<?php
session_start();  // Start session

require 'config.php';
$aadhaar_number = $_SESSION['aadhaar_number'] ?? '';
$captured_fingerprint = $_SESSION['captured_fingerprint'] ?? '';

if (!$aadhaar_number || !$captured_fingerprint) {
    die("Invalid session data.");
}

// Fetch stored fingerprint
$sql = "SELECT index_f FROM aadhaar_data WHERE aadhaar_number = '$aadhaar_number'";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("Aadhaar number not found.");
}
$row = $result->fetch_assoc();
$stored_fingerprint = $row['index_f'];

// FingerprintAPI verification
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

$verification_result = "Error: Verification failed!";
$result_class = "fail";
if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData["matched"] === 1) {
        $verification_result = "✅ Fingerprints Match!";
        $result_class = "success";
    } else {
        $verification_result = "❌ Fingerprints Do Not Match!";
    }
}

curl_close($ch);
session_destroy();  // Clear session data after verification
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Result</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #111; color: white; }
        .result { font-size: 1.5em; padding: 20px; margin-top: 20px; }
        .success { color: green; animation: blink 1s infinite; }
        .fail { color: red; animation: blink 1s infinite; }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.3; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>

    <h2>Verification Result for Aadhaar: <?= $aadhaar_number ?></h2>
    <div class="result <?= $result_class ?>"><?= $verification_result ?></div>

    <?php
    // Check if the fingerprint matches and prepare the audio
    if ($verification_result == '✅ Fingerprints Match!') {
        echo '<audio id="audio" preload="auto" style="display:none;">
                <source src="https://biometric.free.nf/1740721319819xx1xc2to-voicemaker.in-speech.mp3" type="audio/mp3">
                Your browser does not support the audio element.
              </audio>';
    }
    ?>

    <script>
        window.onload = function() {
            // Check if the verification result matches and play the audio
            var verificationResult = "<?= $verification_result ?>";
            if (verificationResult === "✅ Fingerprints Match!") {
                var audio = document.getElementById('audio');
                audio.play().catch(function(error) {
                    console.log("Audio playback failed:", error);
                });
            }
        };
    </script>

</body>
</html>
