<?php
session_start();
require 'config.php';
$sql = "SELECT id, election_name FROM elections";  
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Select an Election</h2>";
    echo "<div class='election-buttons'>";
    while ($row = $result->fetch_assoc()) {
        echo "<a class='election-btn' href='view_results.php?election_id=" . $row['id'] . "'>" . $row['election_name'] . "</a>";
    }
    echo "</div>";
} else {
    echo "<p>No elections found.</p>";
}

$conn->close();
?>
<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f9;
    color: #333;
    line-height: 1.6;
}


header {
    background-color: #0066cc;
    color: white;
    padding: 30px 0;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

header h1 {
    font-size: 36px;
    font-weight: bold;
    margin: 0;
}


.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 20px;
}

h2 {
    font-size: 30px;
    margin-bottom: 40px;
    text-align: center;
    color: #003366;
}

.election-buttons {
    display: flex;
    justify-content: center; 
    align-items: center;      
    flex-wrap: wrap;          
}

.election-btn {
    display: inline-block;
    background-color: #0066cc;
    color: white;
    font-size: 22px;
    padding: 20px 40px;
    margin: 15px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    width: 300px;
    text-align: center;
}

.election-btn:hover {
    background-color: #004080;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}


footer {
    background-color: #003366;
    color: white;
    padding: 20px 0;
    text-align: center;
    margin-top: 30px;
}

footer p {
    font-size: 14px;
}


@media (max-width: 768px) {
    h2 {
        font-size: 24px;
    }

    .election-btn {
        font-size: 18px;
        padding: 15px 30px;
        width: 250px;
    }
}


</style>