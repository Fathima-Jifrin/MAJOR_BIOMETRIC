<?php
require 'config.php' ;

$data = json_decode(file_get_contents("php://input"), true);
$election_id = $data['election_id'];
$voters = $data['voters'];

if (!empty($voters)) {
    foreach ($voters as $epic) {
        $delete_query = "DELETE FROM election_voters WHERE election_id = '$election_id' AND voter_id = '$epic'";
        mysqli_query($conn, $delete_query);
    }
    echo json_encode(["message" => "Selected voters removed successfully."]);
} else {
    echo json_encode(["message" => "No voters selected."]);
}
?>
