<?php

$vote_time = date('Y-m-d H:i:s'); 


$user_ip = $_SERVER['REMOTE_ADDR'];


$user_agent = $_SERVER['HTTP_USER_AGENT'];
$browser = "Unknown Browser";
$os = "Unknown OS";


if (preg_match('/MSIE/i', $user_agent) || preg_match('/Trident/i', $user_agent)) {
    $browser = 'Internet Explorer';
} elseif (preg_match('/Firefox/i', $user_agent)) {
    $browser = 'Mozilla Firefox';
} elseif (preg_match('/Chrome/i', $user_agent)) {
    $browser = 'Google Chrome';
} elseif (preg_match('/Safari/i', $user_agent)) {
    $browser = 'Safari';
} elseif (preg_match('/Opera/i', $user_agent)) {
    $browser = 'Opera';
}

if (preg_match('/win/i', $user_agent)) {
    $os = 'Windows';
} elseif (preg_match('/mac/i', $user_agent)) {
    $os = 'Mac OS';
} elseif (preg_match('/linux/i', $user_agent)) {
    $os = 'Linux';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 80%;
            max-width: 700px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #2c3e50;
            font-weight: bold;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }

        .receipt-details {
            margin: 20px 0;
            text-align: left;
            font-size: 16px;
        }

        .receipt-details p {
            margin: 5px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

        button.print-btn {
            background-color: #2c3e50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        button.print-btn:hover {
            background-color: #3498db;
        }

    </style>
</head>
<body>

    <div class="receipt-container">
       
         <img src="../logo.png" alt="Election Commission Logo">

        <h2>Alappuzha Localbody Election Vote Receipt</h2>

      
        <div class="receipt-details">
            <p><strong>Vote Date & Time:</strong> <?php echo $vote_time; ?></p>
            <p><strong>Your IP Address:</strong> <?php echo $user_ip; ?></p>
            <p><strong>Browser:</strong> <?php echo $browser; ?></p>
            <p><strong>Operating System:</strong> <?php echo $os; ?></p>
        </div>

       
        <button class="print-btn" onclick="window.print()">Print Receipt</button>

        <div class="footer">
            <p>&copy; 2025 Election Commission of India. All Rights Reserved.</p>
        </div>
    </div>

</body>
</html>
