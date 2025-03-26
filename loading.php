<?php
session_start();
$isVerified = false; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    
    if ($entered_otp == $_SESSION['otp']) {
        
        $_SESSION['isVerified'] = true;
        header("Location: otp_verification_result.php");
        exit();
    } 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifying OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            flex-direction: column;
        }
        .message {
            font-size: 24px;
            color: #333;
            animation: fadeIn 2s ease-out;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
    <script>
        setTimeout(function () {
            
            window.location.href = 'otp_verification_result.php';
        }, 3000); 
    </script>
</head>
<body>
    <img src="logo.png" alt="Election Commission Logo" class="logo"> 
    <div class="message">Verifying OTP, please wait...</div>
</body>
</html>
