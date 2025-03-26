<?php
require 'config.php';


$election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
$constituency = isset($_GET['constituency']) ? $_GET['constituency'] : '';


if (empty($election_id) || empty($constituency)) {
    die("Election ID or Constituency is missing.");
}


$voter_query = "
    SELECT vr.*, vi.constituency, vi.EPIC
    FROM online_voting_requests vr
    JOIN voter_id vi ON vr.aadhaar_number = vi.aadhaar_number
    WHERE vi.constituency = '$constituency' AND vr.status = 'approved' AND vr.election_id='$election_id'
";

$voter_result = mysqli_query($conn, $voter_query);

if (!$voter_result) {
    die("Query failed: " . mysqli_error($conn));  // Debugging: print SQL error if query fails
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Voters for Election</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            text-align: center;
            margin-top: 50px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #003366;
            color: white;
        }
        .btn-details {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .btn-details:hover {
            background-color: #45a049;
        }

        header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
        }
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .navbar {
            list-style-type: none;
            padding: 0;
        }
        .navbar li {
            display: inline;
            margin-right: 20px;
        }
        .navbar li a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <header>
        <h1>Online Voting System</h1>
        <ul class="navbar">
            <li><a href="index.php">Home</a></li>
            <li><a href="elections.php">Elections</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </header>

    <div class="container">
        <h2>Approved Voters for Election: <?php echo htmlspecialchars($election_id); ?></h2>
        <p><strong>Constituency:</strong> <?php echo htmlspecialchars($constituency); ?></p>

    
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Aadhaar Number</th>
                    <th>Election ID</th>
                    <th>Status</th>
                    <th>More Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($voter_result) > 0) {
                    while ($row = mysqli_fetch_assoc($voter_result)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['aadhaar_number']) . "</td>
                                <td>" . htmlspecialchars($row['election_id']) . "</td>
                                <td>" . htmlspecialchars($row['status']) . "</td>
                                <td><button class='btn-details' onclick='showDetails(" . $row['id'] . ")'>More Details</button></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No approved voters found for this constituency.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2025 Online Voting System | All rights reserved</p>
    </footer>

    <script>
        function showDetails(voterId) {
           
            window.location.href = 'voter_details.php?id=' + voterId;
        }
    </script>

</body>
</html>
