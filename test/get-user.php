<?php
require 'config.php';

if (isset($_GET['roomId'])) {
    $roomId = $_GET['roomId'];

    $stmt = $conn->prepare("SELECT user_peer_id FROM user_streams WHERE room_id = ?");
    $stmt->bind_param("s", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        echo json_encode($userData); 
    } else {
        echo json_encode(["message" => "No user found for this room"]);
    }

   
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request: roomId missing"]);
}

$conn->close();
?>
