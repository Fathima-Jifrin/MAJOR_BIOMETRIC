<?php
require 'config.php' ;


$election_id = $_GET['election_id'];
$constituency = $_GET['constituency'];


function calculateAge($dob) {
    $dobDate = new DateTime($dob);
    $currentDate = new DateTime();
    $age = $dobDate->diff($currentDate);
    return $age->y;
}


$election_query = "SELECT * FROM elections WHERE id = '$election_id' AND status = 'active'";
$election_result = mysqli_query($conn, $election_query);
$election = mysqli_fetch_assoc($election_result);


$voter_query = "
    SELECT vr.*, vi.constituency, vi.EPIC, vi.date_of_birth 
    FROM voter_registration vr
    JOIN voter_id vi ON vr.aadhaar_number = vi.aadhaar_number
    WHERE vi.constituency = '$constituency' AND vr.astatus = 'approved'
";


$election_voters_query = "
    SELECT voter_id 
    FROM election_voters 
    WHERE election_id = '$election_id'
";
$election_voters_result = mysqli_query($conn, $election_voters_query);
$election_voters = [];
while ($row = mysqli_fetch_assoc($election_voters_result)) {
    $election_voters[] = $row['voter_id'];
}

$voter_result = mysqli_query($conn, $voter_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Voters for Election: <?php echo $election['election_name']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="logo.png" alt="Logo" class="logo">
        <h1>Election Commission of India</h1>
    </header>

    <button class="t" onclick="goBack()">Back</button>
    <div class="container">
        <h2>Select Voters for Election: <?php echo $election['election_name']; ?></h2>
        <p><strong>Constituency:</strong> <?php echo $constituency; ?></p>

        
       
        
        <label><input type="checkbox" id="select-all"> Select All</label>
        <br><br>

        
        <table border="1" id="voter-table">
            <thead>
                <tr>
                    <th>Select</th>
                     <th>EPIC Number</th>
                    <th>Full Name</th>
                    <th>Aadhaar Number</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Constituency</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($voter_result) > 0) {
                    while ($row = mysqli_fetch_assoc($voter_result)) {
                        
                        $age = calculateAge($row['date_of_birth']);
                        
                        if (in_array($row['EPIC'], $election_voters)) {
                            continue; 
                        }
                        echo "<tr>
                                <td><input type='checkbox' class='voter-checkbox' value='" . $row['EPIC'] . "'></td>
                                <td>" . $row['EPIC'] . "</td>
                                <td>" . $row['full_name'] . "</td>
                                <td>" . $row['aadhaar_number'] . "</td>
                                <td>" . $row['phone_number'] . "</td>
                                <td>" . $row['email'] . "</td>
                                <td>" . $age . "</td>
                                <td>" . $row['constituency'] . "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No voters found for this constituency.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        
        <button id="add-voters-btn" onclick="addVoters()">Add Selected Voters to Election List</button>
    </div>

    <script>
        
    </script>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Election Commission. All rights reserved.</p>
    </footer>
</body>
</html>


 <script>

   
    function goBack() {
            window.location.href = "test.php";  
        }

        
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.voter-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

       function addVoters() {
    const selectedVoters = [];
    const checkboxes = document.querySelectorAll('.voter-checkbox:checked');
    checkboxes.forEach(checkbox => {
        selectedVoters.push(checkbox.value);
    });

    if (selectedVoters.length > 0) {
        
        fetch('add_voters_to_election.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                election_id: <?php echo $election_id; ?>,
                voters: selectedVoters,
            })
        })
        .then(response => response.text())  
        .then(data => {
            console.log("Response received:", data);  
            try {
                let jsonData = JSON.parse(data);  
                alert(jsonData.message);  
                
                
                if (jsonData.message === "Voters added successfully") {
                    window.location.href = "select_voters.php?election_id=" + <?php echo $election_id; ?> + "&constituency=" + "<?php echo $constituency; ?>";  // Redirect to the select_voters page
                }
            } catch (error) {
                alert("Invalid JSON response. Check console for details.");
                console.error("Parsing error:", error, "Response:", data);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });

    } else {
        alert("Please select at least one voter.");
    }
}

    </script>




<style>
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