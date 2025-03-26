<?php
session_start();

require 'config.php';


$vote_id = '1'; 

$sql = "SELECT election_id, candidate_id, encrypted_vote, nonce FROM votes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vote_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($encrypted_election_id, $encrypted_candidate_id, $encrypted_vote, $nonce_base64);

if ($stmt->fetch()) {
    
    $encrypted_election_id = base64_decode($encrypted_election_id);
    $encrypted_candidate_id = base64_decode($encrypted_candidate_id);
    $encrypted_vote = base64_decode($encrypted_vote);
    $nonce = base64_decode($nonce_base64);

    
    $secret_key = sodium_crypto_secretbox_keygen();

    $decrypted_election_id = sodium_crypto_secretbox_open($encrypted_election_id, $nonce, $secret_key);
    $decrypted_candidate_id = sodium_crypto_secretbox_open($encrypted_candidate_id, $nonce, $secret_key);

    
    $decrypted_vote = sodium_crypto_secretbox_open($encrypted_vote, $nonce, $secret_key);

    if ($decrypted_election_id === false || $decrypted_candidate_id === false || $decrypted_vote === false) {
        echo "Decryption failed!";
    } else {
       
        echo "Election ID: " . htmlspecialchars($decrypted_election_id) . "<br>";
        echo "Candidate ID: " . htmlspecialchars($decrypted_candidate_id) . "<br>";
        echo "Vote Data: " . htmlspecialchars($decrypted_vote) . "<br>";
    }
} else {
    echo "No vote found with the given ID.";
}


$stmt->close();
$conn->close();
?>
