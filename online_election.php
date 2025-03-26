<?php

require 'config.php' ;


$election_query = "SELECT * FROM elections WHERE status = 'active'";
$election_result = mysqli_query($conn, $election_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Election</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        footer {
            background-color: #003366;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .container {
            padding: 50px 20px;
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .election-btn {
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            margin: 10px;
            border: none;
            cursor: pointer;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .election-btn:hover {
            background-color: #45a049;
        }

        .election-btn:active {
            background-color: #388E3C;
        }

        h2 {
            font-size: 32px;
            color: #003366;
            margin-bottom: 30px;
        }

        .elections-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .election-btn-container {
            margin: 10px;
        }

        @media (max-width: 768px) {
            .election-btn {
                font-size: 16px;
                padding: 12px 25px;
            }

            .elections-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<header>
    <img src="logo.png" alt="Logo" style="height: 50px; vertical-align: middle; margin-right: 15px;">
    Election Commission of India
</header>

<div class="container">
    <h2>Select Election to Add Approved Voters</h2>

    
    <div class="elections-container">
        <?php
        if (mysqli_num_rows($election_result) > 0) {
            while ($row = mysqli_fetch_assoc($election_result)) {
                echo "<div class='election-btn-container'>
                        <button class='election-btn' onclick='selectElection(" . $row['id'] . ", \"" . $row['constituency'] . "\")'>" . $row['election_name'] . "</button>
                    </div>";
            }
        } else {
            echo "<p>No active elections found.</p>";
        }
        ?>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Election Commission of India. All rights reserved.
</footer>

<script>
    function selectElection(electionId, constituency) {
        
        window.location.href = 'approved_voters.php?election_id=' + electionId + '&constituency=' + constituency;
    }
</script>

</body>
</html>
