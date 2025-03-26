<?php
session_start();


if (!isset($_SESSION['officer_id'])) {
    die("Access Denied. Please login first.");
}

require 'config.php' ;


$officer_id = $_SESSION['officer_id'];
$officer_query = "SELECT con FROM election_officers WHERE officer_id = ?";
$stmt = $conn->prepare($officer_query);
$stmt->bind_param("i", $officer_id);
$stmt->execute();
$stmt->bind_result($officer_constituency);
$stmt->fetch();
$stmt->close();

if (!$officer_constituency) {
    die("Officer constituency not found. Please check your details.");
}


$election_id = isset($_GET['id']) ? $_GET['id'] : '';


$sql = "SELECT o.*, v.epic, v.constituency, e.election_name 
        FROM online_voting_requests o 
        LEFT JOIN voter_id v ON o.aadhaar_number = v.aadhaar_number 
        LEFT JOIN elections e ON o.election_id = e.id
        WHERE o.status = 'approved' 
        AND v.constituency = ?";

if (!empty($election_id)) {
    $sql .= " AND o.election_id = ?";
}

$stmt = $conn->prepare($sql);
if (!empty($election_id)) {
    $stmt->bind_param("si", $officer_constituency, $election_id);
} else {
    $stmt->bind_param("s", $officer_constituency);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Online Voters - Election Commission of India</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .logo {
            position: center;
            height: 50px;
        }
        header {
            text-align: center;
            background-color: darkblue;
            color: white;
            padding: 10px 0;
            margin-bottom: 20px;
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
        .container {
            width: 90%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
      h2 {
    text-align: center;
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #003366;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        .filter-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <img src="logo.png" alt="Logo" class="logo">
    <h1>Election Commission of India</h1>
</header>

<div class="container">
    <h2>Approved Online Voters - Constituency: <?php echo htmlspecialchars($officer_constituency); ?></h2>

   
 
   

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
                <th>EPIC</th>
                <th>Name</th>
                <th>Aadhaar Number</th>
                <th>Reason</th>
                <th>Additional Info</th>
                <th>Election</th>
                <th>Constituency</th>
                <th>Proof File</th>
                <th>Request Date</th>
                <th>Status</th>
            </tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['epic']}</td>
                <td>{$row['name']}</td>
                <td>{$row['aadhaar_number']}</td>
                <td>{$row['reason']}</td>
                <td>{$row['additional_info']}</td>
                <td>{$row['election_name']}</td>
                <td>{$row['constituency']}</td>
                <td><a href='{$row['proof_file_path']}' target='_blank'>{$row['proof_file_name']}</a></td>
                <td>{$row['request_date']}</td>
                <td>{$row['status']}</td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No approved voters found in your constituency for this election.</p>";
    }

    $conn->close();
    ?>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Election Commission of India. All Rights Reserved.
</footer>

</body>
</html>
