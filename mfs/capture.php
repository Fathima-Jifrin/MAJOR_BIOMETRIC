<?php
// Base URI for the MFScan API


// Function to send POST requests to the MFScan API
function postMFScanClient($method, $data = null) {
    $uri = "https://localhost:8034/mfscan/";
    $url = $uri . $method;
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Disable SSL verification

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'error' => $error,
        ];
    }

    curl_close($ch);
    return [
        'success' => $httpCode === 200,
        'data' => json_decode($response, true),
    ];
}

// Function to capture fingerprint
function captureFinger($quality, $timeout) {
    $data = [
        "Quality" => $quality,
        "TimeOut" => $timeout
    ];
    return postMFScanClient("capture", $data);
}

// Capture fingerprint
$response = captureFinger(80, 10); // Quality: 80, Timeout: 10 seconds

if ($response['success'] && isset($response['data']['BitmapData'])) {
    echo json_encode([
        'success' => true,
        'data' => $response['data']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $response['data']['ErrorDescription'] ?? 'Unknown error'
    ]);
}
?>
