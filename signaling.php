<?php
session_start();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['type'])) {
        
        $_SESSION[$data['type']] = $data['data'];
        echo json_encode(["status" => "{$data['type']}_saved"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $type = $_GET['type'] ?? null;
    if ($type && isset($_SESSION[$type])) {
        echo json_encode(["data" => $_SESSION[$type]]);
        unset($_SESSION[$type]); 
    } else {
        echo json_encode(["data" => null]);
    }
}
