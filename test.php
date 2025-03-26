<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Election</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <header>
        <img src="logo.png" alt="Election Commission Logo">
        <h1>Election Commission of India</h1>
    </header>
<div class="text-center mt-6">
    <button onclick="window.location.href='electdash.html'" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-700">
        Back to Election Dashboard
    </button>
</div>

    
    <div class="container">
        <h2>Select Election to Manage Voters</h2>

        
        <div id="elections">
            <?php
                require 'config.php' ;

                $election_query = "SELECT * FROM elections WHERE status = 'active'";
                $result = mysqli_query($conn, $election_query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $electionId = htmlspecialchars($row['id']);
                        $electionName = htmlspecialchars($row['election_name']);
                        $constituency = htmlspecialchars($row['constituency']);

                        echo "<div class='election-group'>
                                <h3>{$electionName} ({$constituency})</h3>
                                <button class='election-btn' onclick='selectElection($electionId, \"$constituency\")'>Add Voters</button>
                                <button class='update-btn' onclick='updateVoters($electionId, \"$constituency\")'>Update Voters List</button>
                                <button class='print-btn' onclick='printVoters($electionId, \"$constituency\")'>Print Voters List</button>
                              </div>";
                    }
                } else {
                    echo "<p>No active elections found.</p>";
                }

                $conn->close();
            ?>
        </div>
    </div>

    
    <footer>
        <p>&copy; 2025 Election Commission of India | <a href="https://eci.gov.in" target="_blank">Official Website</a></p>
    </footer>

    
    <script>
        function selectElection(electionId, constituency) {
            window.location.href = 'select_voters.php?election_id=' + electionId + '&constituency=' + constituency;
        }

        function updateVoters(electionId, constituency) {
            window.location.href = 'update_voterlist.php?election_id=' + electionId + '&constituency=' + constituency;
        }

        function printVoters(electionId, constituency) {
            window.location.href = 'election_listprint.php?election_id=' + electionId + '&constituency=' + constituency;
        }
    </script>
</body>
</html>

<style>

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


header {
    background-color: #003366;
    color: white;
    padding: 20px 0;
    text-align: center;
}

header img {
    width: 100px;  
    margin-bottom: 10px;
}

header h1 {
    font-size: 24px;
    margin-top: 10px;
}


.container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
}


h2 {
    font-size: 26px;
    color: #003366;
    text-align: center;
    margin-bottom: 20px;
}

#elections {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}


.election-group {
    background: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 280px;
}

.election-group h3 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #003366;
}


button {
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
    margin-bottom: 10px;
}

button.election-btn {
    background-color: #003366;
    color: white;
}

button.election-btn:hover {
    background-color: #0056b3;
}

button.update-btn {
    background-color: #ff9800;
    color: white;
}

button.update-btn:hover {
    background-color: #e68900;
}

button.print-btn {
    background-color: #4caf50;
    color: white;
}

button.print-btn:hover {
    background-color: #45a049;
}


footer {
    background-color: #003366;
    color: white;
    padding: 15px;
    text-align: center;
    font-size: 14px;
    position: fixed;
    width: 100%;
    bottom: 0;
}


footer a {
    color: #ffffff;
    text-decoration: none;
    font-weight: bold;
}

footer a:hover {
    text-decoration: underline;
}

</style>
