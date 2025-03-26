<?php
header('Content-Type: application/json');

if (!isset($_GET['election_id'])) {
    echo json_encode(["error" => "Invalid election ID"]);
    exit;
}

$election_id = intval($_GET['election_id']);
$conn = new mysqli("localhost", "root", "", "election_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}


$query = "
    SELECT COUNT(*) as votes, DATE_FORMAT(vote_time, '%H:%i') as time_label 
    FROM votes 
    WHERE election_id = $election_id 
    GROUP BY time_label 
    ORDER BY vote_time
";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(["error" => "SQL Error: " . $conn->error]);
    exit;
}


$timestamps = [];
$votes = [];

while ($row = $result->fetch_assoc()) {
    $timestamps[] = $row['time_label'];  
    $votes[] = $row['votes'];
}


$response = ["timestamps" => $timestamps, "votes" => $votes];

if (empty($timestamps)) {
    $response["message"] = "No vote data found for this election ID";
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
