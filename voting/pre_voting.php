<?php
session_start();
require 'config.php';
$aadhaar=$_SESSION['aadhaar_number'];
$sql = "SELECT id, voter_id, election_id, name, aadhaar_number FROM online_voting_requests WHERE aadhaar_number = ?";

$stmt = $conn->prepare($sql);


$stmt->bind_param("s", $aadhaar);


$stmt->execute();

$stmt->bind_result($id, $voter_id, $election_id, $name, $aadhaar_number);

if ($stmt->fetch()) {
    
    $_SESSION['election_id'] = $election_id;
    $_SESSION['id'] = $id;
    $_SESSION['voter_id'] = $voter_id;
    $_SESSION['name'] = $name;


} else {
    echo "No records found for this Aadhaar number.";
}

$stmt->close();



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Animation</title>
    <style>
       
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #333;
        }

       
        .animation-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        
        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
        }

        p {
            margin-bottom: 20px;
            font-size: 18px;
            line-height: 1.5;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 25px;
            overflow: hidden;
            margin-top: 30px;
        }

        .progress-bar span {
            display: block;
            height: 100%;
            background-color: #007bff;
            width: 0%;
            border-radius: 25px;
            animation: progress 5s forwards;
        }

        @keyframes progress {
            0% {
                width: 0%;
            }
            100% {
                width: 100%;
            }
        }


        .btn {
            background-color: #007bff;
            padding: 12px 25px;
            font-size: 18px;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .bharat-flag {
            position: absolute;
            bottom: 10px;
            left: 10px;
            width: 40px;
        }
    </style>
</head>
<body>

    <div class="animation-container">
        <h2>Welcome to the Online Voting System</h2>
        <p>Your vote is safe and securely encrypted with end-to-end encryption.</p>
        <p>Empowering democracy through secure and transparent technology.</p>

        <div class="progress-bar">
            <span></span>
        </div>

        <div>
            <button class="btn" onclick="window.location.href = 'voting.php'">Proceed to Voting</button>
        </div>
    </div>

    <img src="bharat-flag.png" class="bharat-flag" alt="Bharat Flag">

    <script>
       
        setTimeout(function() {
            window.location.href = "voting.php";
        }, 5000); 
    </script>

</body>
</html>
