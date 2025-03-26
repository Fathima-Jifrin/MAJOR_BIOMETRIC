<?php
require 'config.php';

$sql = "SELECT * FROM elections where online_voting != 'cancelled'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Activation</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
        }
        header {
            text-align: center;
            background-color: darkblue;
            color: white;
            padding: 10px 0;
            margin-bottom: 20px;
            position: relative;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 12px rgba(0,0,0,0.1);
            background-color: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            padding: 20px 40px;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .active {
            background-color: green;
            color: white;
        }
        .inactive {
   
            background-color: yellow;
            color: black;
        }
          .deactive {
            background-color: red;
            color: white;
        }
        .back-button {
            background-color: #4CAF50; 
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #45a049; 
        }
    </style>
</head>
<body>
  <header>
            <img src="../logo.png" alt="Logo" class="logo" width="100" height="100">
            <h1>Election Commission of India</h1>
        </header>
        <button onclick="window.location.href='../online.php';">Back to Online</button>

    <div class="container">
        <h2>Available Elections</h2>
        <table>
            <tr>
                <th>Election ID</th>
                <th>Election Name</th>
                <th>State</th>
                <th>Constituency</th>
                <th>Procedure Start Date</th>
                <th>Election Date</th>
                <th>Election Type</th>
                <th>Online Voting Status</th>
                <th>Set Activation</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['election_name']; ?></td>
                <td><?php echo $row['state']; ?></td>
                <td><?php echo $row['constituency']; ?></td>
                <td><?php echo $row['procedure_start_date']; ?></td>
                <td><?php echo $row['election_date']; ?></td>
                <td><?php echo $row['election_type']; ?></td>
                <td>
                    <?php 
                    $online_voting_status = $row['online_voting'];
                    if ($online_voting_status == 'active') {
                        echo "<span class='active'>Active</span>";
                    } 
                    else if($online_voting_status == 'deactivate'){
                        echo "<span class='deactive'>Deactive</span>";
                    }
                    
                    else {
                        echo "<span class='inactive'>Inactive</span>";
                    }
                    ?>
                </td>
                <td><a class="btn" href="set_election.php?id=<?php echo $row['id']; ?>">Set Date</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <div class="footer">&copy; 2025 Election Commission of India. All rights reserved.</div>
</body>
</html>
