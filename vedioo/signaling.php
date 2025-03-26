<?php

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');


require 'config.php';

$user_id = $_GET['user_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($user_id && isset($data['offer'])) {
        $stmt = $conn->prepare("INSERT INTO signaling (user_id, offer) VALUES (?, ?) ON DUPLICATE KEY UPDATE offer=?");
        $stmt->bind_param("sss", $user_id, json_encode($data['offer']), json_encode($data['offer']));
        $stmt->execute();
    } elseif ($user_id && isset($data['answer'])) {
        $stmt = $conn->prepare("UPDATE signaling SET answer=? WHERE user_id=?");
        $stmt->bind_param("ss", json_encode($data['answer']), $user_id);
        $stmt->execute();
    } elseif ($user_id && isset($data['candidate'])) {
        $stmt = $conn->prepare("UPDATE signaling SET candidate=? WHERE user_id=?");
        $stmt->bind_param("ss", json_encode($data['candidate']), $user_id);
        $stmt->execute();
    }
    echo json_encode(["status" => "Data stored successfully"]);
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $user_id) {
    $stmt = $conn->prepare("SELECT offer, answer, candidate FROM signaling WHERE user_id=?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result ?: []);
}

$conn->close();
?>
