
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require 'config.php';


$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['election_id']) || !isset($data['voters'])) {
    echo json_encode(["error" => "Invalid request parameters"]);
    exit;
}

$election_id = $data['election_id'];
$voters = $data['voters'];

foreach ($voters as $voter_id) {
    $sql = "INSERT INTO election_voters (election_id, voter_id) VALUES ('$election_id', '$voter_id')";
    if (!$conn->query($sql)) {
        echo json_encode(["error" => "Error adding voter: " . $conn->error]);
        exit;
    }
}

echo json_encode(["message" => "Voters added successfully"]);
exit;


header('Location: test.php');
exit;
?>
