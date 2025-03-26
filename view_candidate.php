<?php

include('config.php');


if (isset($_GET['id'])) {
    $election_id = $_GET['id'];
  $query = "SELECT election_name FROM elections WHERE  id = '$election_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();
        $election_name = $row['election_name'];
    }
    
    $sql = "SELECT id, name, party, constituency, age, candidate_image FROM candidates WHERE election_id = '$election_id' AND verified = 'Verified'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        
        $candidates = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<script>alert('No verified candidates found for Election ID: $election_name'); window.location.href='verifiedcandidate.php';</script>";
        exit;
    }
} else {
    echo "Election ID is required.";
    exit;
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="styles.css">
</head>

    <header>
        <div class="logo">
            <img src="logo.png" alt="Election Commission of India Logo">
        </div>
        <h2 class="test">Election Commission of India</h2>
    </header>
    <div class="back-button-container">
        <a href="verifiedcandidate.php" class="back-button">← Back</a>
    </div>
<body>
    <div class="container">
        <h1>Verified Candidates for: <?php echo htmlspecialchars($election_name); ?></h1>
        <table class="candidate-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Party</th>
                    <th>Constituency</th>
                    <th>Age</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($candidates as $index => $candidate) {
                    echo "<tr>";
                    echo "<td>" . ($index + 1) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($candidate['candidate_image']) . "' alt='Candidate Image' width='250' height='100' class='table-image'></td>";
                    echo "<td>" . htmlspecialchars($candidate['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($candidate['party']) . "</td>";
                    echo "<td>" . htmlspecialchars($candidate['constituency']) . "</td>";
                    echo "<td>" . htmlspecialchars($candidate['age']) . "</td>";
                    echo "<td><a href='viewfinalcandidate.php?id=" . htmlspecialchars($candidate['id']) . "' class='details-button'>View Details</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
<style>



body, h1, table, th, td {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    
}
h2{
    color:white;
}
header {
    background-color:darkblue;
    color: #fff;
    padding: 20px;
    text-align: center;
}
.back-button-container {
            margin-top: 30px; 
            padding-left: 20px; 
        }

        .back-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: orange;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #004494;
        }
footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 20px 0;
    position: relative;
    bottom: 0;
    width: 100%;
}

.logo img {
    width: 60px; 
}

body {
    font-family: 'Arial', sans-serif;
    font-size: 16px;
    background-color: #f7f7f7;
    color: #333;
}


.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}


h1 {
    text-align: center;
    color: #003366;
    font-size: 1.8rem;
    margin-bottom: 20px;
    text-transform: uppercase;
}


.candidate-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    text-align: center;
}

.candidate-table thead {
    background-color: #003366;
    color: #fff;
}

.candidate-table th,
.candidate-table td {
    border: 1px solid #ddd;
    padding: 10px;
    font-size: 0.9rem;
}

.candidate-table tbody tr:nth-child(even) {
    background-color: #f1f5fc;
}

.candidate-table tbody tr:nth-child(odd) {
    background-color: #fff;
}

.candidate-table tbody tr:hover {
    background-color: #f1f1f1;
}


.table-image {
    max-width: 50px;
    border-radius: 5px;
}


.details-button {
    display: inline-block;
    background: #007bff;
    color: #fff;
    font-size: 0.9rem;
    font-weight: bold;
    text-transform: uppercase;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
}

.details-button:hover {
    background: #0056b3;
}


@media screen and (max-width: 768px) {
    .candidate-table {
        font-size: 0.8rem;
    }

    .table-image {
        max-width: 40px;
    }

    .details-button {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
}


</style>