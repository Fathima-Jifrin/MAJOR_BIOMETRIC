<?php
session_start();
require 'config.php';

$aadhaar_number = $_GET['aadhaar'] ?? '';
if (!$aadhaar_number) {
    die("Invalid Aadhaar number.");
}

// MFScan API Base URI

function postMFScanClient($method, $data = null) {
    $uri = "https://localhost:8034/mfscan/";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = postMFScanClient("capture", ["Quality" => 80, "TimeOut" => 10000]);

    if ($response['success'] && isset($response['data']['BitmapData'])) {
        $_SESSION['aadhaar_number'] = $aadhaar_number;
        $_SESSION['captured_fingerprint'] = $response['data']['BitmapData'];

        echo json_encode(['success' => true, 'redirect' => 'verify1.php']);
    } else {
        echo json_encode(['success' => false, 'error' => $response['data']['ErrorDescription'] ?? 'Unknown error']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fingerprint Scan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #111;
            color: white;
        }
        .scan-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    
        .fingerprint {
            position: relative;
            width: 250px;
            height: 300px;
            background: url('https://raw.githubusercontent.com/lasithadilshan/fingerprintanimation.github.io/main/fingerPrint_01.png') no-repeat center;
            background-size: contain;
            border-radius: 15px;
            border: 2px solid #00ff00;
            box-shadow: 0 0 15px #00ff00;
            transition: box-shadow 0.5s ease-in-out;
        }

        .fingerprint::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #00ff00;
            mix-blend-mode: multiply;
            opacity: 0.8;
            border-radius: inherit;
        }

        .scan-line {
            position: absolute;
            width: 100%;
            height: 5px;
            background: #3fefef;
            box-shadow: 0 0 10px #3fefef;
            animation: scan-animation 2.5s linear infinite;
        }

        @keyframes scan-animation {
            0% { top: 0%; opacity: 1; }
            50% { top: 100%; opacity: 0.6; }
            100% { top: 0%; opacity: 1; }
        }

        .success-glow {
            box-shadow: 0 0 40px #00ff00 !important;
            border-color: #00ff00 !important;
        }

        h3 {
            font-size: 1.8em;
            color: #3fefef;
            animation: blink-text 1s steps(2, start) infinite;
        }

        @keyframes blink-text {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }

        #result {
            font-size: 1.5em;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Scanning Fingerprint for Aadhaar: <?= htmlspecialchars($aadhaar_number) ?></h2>
    <div class="scan-container">
        <div class="fingerprint" id="fingerprint">
            <div class="scan-line"></div>
        </div>
        <h3>Scanning...</h3>
        <div id="result"></div>
    </div>

    <script>
        setTimeout(() => {
            fetch("", { method: "POST", headers: { "Content-Type": "application/json" } })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("fingerprint").classList.add("success-glow");
                        document.getElementById("result").innerHTML = `<span style="color:rgb(82, 236, 82);">Scan Successful!</span><br><span style="color:rgb(82, 236, 82);">Verification in Progress!</span>`;


                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000); // Redirect after 2 seconds
                    } else {
                        document.getElementById("result").innerHTML = `<span style="color: red;">Error: ${data.error}</span>`;
                    }
                })
                .catch(error => document.getElementById("result").innerHTML = `<span style="color: red;">An error occurred: ${error}</span>`);
        }, 4000);
    </script>

</body>
</html>
