<?php

session_start();

require 'config.php' ;


$states_sql = "SELECT DISTINCT state FROM elections ORDER BY state";
$states_result = $conn->query($states_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Elections</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        header {
            background-color: darkblue;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: darkblue;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #333;
            color: white;
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
    <script>
        function fetchElections() {
            const state = document.getElementById('state').value;
            if (state) {
                window.location.href = `?state=${state}`;
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Election Commission of India</h1>
    </header>

    <div class="container">
        <h1>View Elections by State</h1>

        
        <label for="state">Select State</label>
        <select id="state" name="state">
            <option value="">-- Select State --</option>
            <?php
            if ($states_result && $states_result->num_rows > 0) {
                while ($row = $states_result->fetch_assoc()) {
                    $selected = (isset($_GET['state']) && $_GET['state'] === $row['state']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row['state']) . "' $selected>" . htmlspecialchars($row['state']) . "</option>";
                }
            }
            ?>
        </select>
        <button onclick="fetchElections()">View Elections</button>

        <?php
        if (isset($_GET['state']) && !empty($_GET['state'])) {
            $state = $_GET['state'];

            
            $sql = "SELECT * FROM elections WHERE state = ? ORDER BY election_date";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $state);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                echo "<h2>Elections in " . htmlspecialchars($state) . "</h2>";
                echo "<table>";
                echo "<tr>
                        <th>Constituency</th>
                        <th>Procedure Start Date</th>
                        <th>Election Date</th>
                        <th>Election Type</th>
                        <th>Description</th>
                        <th>Total Active Voters</th>
                                 <th>Regular  Voters</th>
                        <th>Online Voters</th>
                      </tr>";

                while ($row = $result->fetch_assoc()) {
                    $constituency = $row['constituency'];

                    
                $voter_sql = "
    SELECT COUNT(*) as total_voters 
    FROM voter_registration vr
    JOIN voter_id vi ON vr.aadhaar_number = vi.aadhaar_number
    WHERE vi.constituency = ? AND vr.astatus = 'approved'";
$voter_stmt = $conn->prepare($voter_sql);
$voter_stmt->bind_param("s", $constituency);
$voter_stmt->execute();
$voter_result = $voter_stmt->get_result();
$total_voters = $voter_result->fetch_assoc()['total_voters'] ?? 0;


$online_voter_sql = "
    SELECT COUNT(*) as online_voters 
    FROM voter_registration vr
    JOIN voter_id vi ON vr.aadhaar_number = vi.aadhaar_number
    WHERE vi.constituency = ? AND vr.astatus = 'approved'AND vi.ovstatus='ov' AND vr.aadhaar_number IS NOT NULL";
$online_voter_stmt = $conn->prepare($online_voter_sql);
$online_voter_stmt->bind_param("s", $constituency);
$online_voter_stmt->execute();
$online_voter_result = $online_voter_stmt->get_result();
$online_voters = $online_voter_result->fetch_assoc()['online_voters'] ?? 0;


                    
   $regular_voter_sql = "
    SELECT COUNT(*) as regular_voters 
    FROM voter_id vi 
    JOIN voter_registration vr ON vr.aadhaar_number = vi.aadhaar_number
    WHERE vi.constituency = ? AND vr.astatus = 'approved' AND vi.ovstatus ='regular'AND vr.aadhaar_number IS NOT NULL";
$regular_voter_stmt = $conn->prepare($regular_voter_sql);
$regular_voter_stmt->bind_param("s", $constituency);
$regular_voter_stmt->execute();
$regular_voter_result = $regular_voter_stmt->get_result();
$regular_voters = $regular_voter_result->fetch_assoc()['regular_voters'] ?? 0;


                    
                    echo "<tr>
                            <td>" . htmlspecialchars($row['constituency']) . "</td>
                            <td>" . htmlspecialchars($row['procedure_start_date']) . "</td>
                            <td>" . htmlspecialchars($row['election_date']) . "</td>
                            <td>" . htmlspecialchars($row['election_type']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>" . htmlspecialchars($total_voters) . "</td>
                              <td>" . htmlspecialchars($regular_voters) . "</td>
                            <td>" . htmlspecialchars($online_voters) . "</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No elections found for " . htmlspecialchars($state) . ".</p>";
            }

            $stmt->close();
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>

<?php

$conn->close();
?>