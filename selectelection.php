<?php
require 'config.php' ;


$sql = "SELECT id, election_name FROM elections WHERE online_voting='inactive'";
$result = $conn->query($sql);

$options = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['id']}'>{$row['election_name']}</option>";
    }
} else {
    $options = "<option value=''>No active elections available</option>";
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
            padding: 0;
        }

        header {
            background-color: darkblue;
            color: white;
            padding: 15px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #FF9933;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e68928;
        }
        h1{
            text-align:center;
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
     <h1>Select Election</h1>
        <form action="addcandidate.php" method="GET">
            <label for="election_id">Select an Active Election</label>
            <select id="id" name="id" required>
                <?= $options ?>
            </select>
            <button type="submit">Proceed</button>
        </form>
    </div>
      <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
