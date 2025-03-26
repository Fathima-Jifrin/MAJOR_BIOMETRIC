<?php
require 'config.php' ;


$sql = "SELECT id, election_name FROM elections WHERE status='active'";
$result = $conn->query($sql);

$buttons = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $buttons .= "
            <form action='viewcandidate.php' method='GET'>
                <button type='submit' name='id' value='{$row['id']}' class='election-btn'>
                    {$row['election_name']}
                </button>
            </form>
        ";
    }
} else {
    $buttons = "<p>No active elections available.</p>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Election</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        header {
            text-align: center;
            background-color: darkblue;
            color: white;
            padding: 15px;
        }

        h1 {
            font-size: 24px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            text-align: center;
        }
        .back-button-container {
            margin-top: 30px; 
            padding-left: 20px; 
        }

        .back-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #004494;
        }
        .election-btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            background-color: #FF9933;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .election-btn:hover {
            background-color: #e68928;
            transform: scale(1.05);
        }

        .election-btn:active {
            transform: scale(0.95);
        }
          footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
 <header>
        <div class="logo">
            <img src="logo.png" alt="ECI Logo" width="100" height="100">
        </div>
        <h1>Election Commission of India</h1>
    </header>
    <div class="back-button-container">
        <a href="candidatedash.php" class="back-button">← Back</a>
    </div>
    <div class="container">
        <?= $buttons ?>
    </div>
        <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
