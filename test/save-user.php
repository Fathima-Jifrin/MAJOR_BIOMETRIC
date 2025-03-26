<?php
require 'config.php';


$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['roomId']) && isset($data['userPeerId'])) {
    $roomId = $data['roomId'];
    $userPeerId = $data['userPeerId'];

    $stmt = $conn->prepare("INSERT INTO user_streams (room_id, user_peer_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $roomId, $userPeerId);

    
    if ($stmt->execute()) {
        echo json_encode(["message" => "User data saved successfully"]);
    } else {
        echo json_encode(["message" => "Error saving user data: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid data: roomId or userPeerId missing"]);
}

$conn->close();
?>
