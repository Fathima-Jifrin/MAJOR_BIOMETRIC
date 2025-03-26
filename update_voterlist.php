<?php
require 'config.php' ;


$election_id = $_GET['election_id'];
$constituency = $_GET['constituency'];


$election_query = "SELECT * FROM elections WHERE id = '$election_id' AND status = 'active'";
$election_result = mysqli_query($conn, $election_query);
$election = mysqli_fetch_assoc($election_result);


$search_epic = isset($_GET['search_epic']) ? $_GET['search_epic'] : '';
$age_filter = isset($_GET['age']) ? (int)$_GET['age'] : '';


$voter_query = "SELECT vr.*, vi.constituency, vi.EPIC, TIMESTAMPDIFF(YEAR, vr.date_of_birth, CURDATE()) AS age 
                FROM voter_registration vr
                JOIN voter_id vi ON vr.aadhaar_number = vi.aadhaar_number
                JOIN election_voters ev ON ev.voter_id = vi.EPIC
                WHERE ev.election_id = '$election_id' AND vi.constituency = '$constituency'";


if (!empty($search_epic)) {
    $voter_query .= " AND vi.EPIC LIKE '%$search_epic%'";
}


if (!empty($age_filter)) {
    $voter_query .= " AND TIMESTAMPDIFF(YEAR, vr.date_of_birth, CURDATE()) = $age_filter";
}

$voter_result = mysqli_query($conn, $voter_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Voters for <?php echo $election['election_name']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="logo.png" alt="Logo" class="logo">
        <h1>Election Commission of India</h1>
    </header>

    <button class="t" onclick="goBack()">Back</button>

    <div class="container">
        <h2>Update Voters for: <?php echo $election['election_name']; ?></h2>
        <p><strong>Constituency:</strong> <?php echo $constituency; ?></p>

        
        <form method="GET">
            <input type="hidden" name="election_id" value="<?php echo $election_id; ?>">
            <input type="hidden" name="constituency" value="<?php echo $constituency; ?>">

            <input type="text" name="search_epic" placeholder="Search by EPIC Number" value="<?php echo $search_epic; ?>">
            <input type="number" name="age" placeholder="Filter by Age" min="18" value="<?php echo $age_filter; ?>">
            <button type="submit">Search</button>
        </form>

        <br>

        
        <table border="1">
            <thead>
                <tr>
                    <th>Remove</th>
                    <th>Full Name</th>
                    <th>EPIC Number</th>
                    <th>Age</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Constituency</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($voter_result) > 0) {
                    while ($row = mysqli_fetch_assoc($voter_result)) {
                        echo "<tr>
                                <td><input type='checkbox' class='voter-checkbox' value='" . $row['EPIC'] . "'></td>
                                <td>" . $row['full_name'] . "</td>
                                <td>" . $row['EPIC'] . "</td>
                                <td>" . $row['age'] . "</td>
                                <td>" . $row['phone_number'] . "</td>
                                <td>" . $row['email'] . "</td>
                                <td>" . $row['constituency'] . "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No voters found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        
        <button id="remove-voters-btn" onclick="removeVoters()">Remove Selected Voters</button>
    </div>

    <script>
        function goBack() {
            window.location.href = "test.php";
        }

        function removeVoters() {
            const selectedVoters = [];
            document.querySelectorAll('.voter-checkbox:checked').forEach(checkbox => {
                selectedVoters.push(checkbox.value);
            });

            if (selectedVoters.length > 0) {
                fetch('remove_voters_from_election.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        election_id: <?php echo $election_id; ?>,
                        voters: selectedVoters,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            } else {
                alert("Please select at least one voter to remove.");
            }
        }
    </script>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Election Commission. All rights reserved.</p>
    </footer>
</body>
</html>
<style>

.search-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 20px;
}


.search-container input[type="text"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 250px;
    transition: 0.3s ease-in-out;
}

.search-container input[type="text"]:focus {
    border-color: #003366;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 51, 102, 0.5);
}


.search-container button {
    background-color: #003366;
    color: white;
    padding: 10px 15px;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.search-container button:hover {
    background-color: #0056b3;
}

.t{
    margin:5px;
    width:150px;
    height:40px;
}
 footer p {
                text-align: center;
                background-color: #0056b3;
                color: white;
                padding: 10px 0;
                margin-bottom: 20px;
            }

 .logo {
            position: center;
            top: 10px;
            left: 10px;
            height: 50px;
        }

   header{
                text-align: center;
                background-color: darkblue;
                color: white;
                padding: 10px 0;
                margin-bottom: 20px;
            }



* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    background-color: #f4f4f4;
    color: #333;
    font-size: 14px;
    line-height: 1.5;
}


.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
}


h2 {
    font-size: 26px;
    color: #003366;
    text-align: center;
    margin-bottom: 20px;
}

p {
    font-size: 18px;
    color: #333;
    margin: 10px 0;
    text-align: center;
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #003366;
    color: white;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

td input[type="checkbox"] {
    margin: 0 auto;
    display: block;
}


button {
    background-color: #003366;
    color: white;
    padding: 10px 20px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    display: block;
    margin: 20px auto;
    width: 200px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

button:disabled {
    background-color: #ddd;
    cursor: not-allowed;
}


footer {
    text-align: center;
    margin-top: 50px;
    font-size: 12px;
    color: #777;
}

footer a {
    text-decoration: none;
    color: #003366;
}

</style>