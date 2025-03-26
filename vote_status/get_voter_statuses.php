<?php

require_once 'db_connection.php'; 


$sql = "SELECT id, voter_id, election_id, online,otp_status,otp_failure_count, biometric_status,biometric_failure_count, voting_status FROM `online_voting_requests`";
$result = mysqli_query($conn, $sql);


$voterStatuses = [];
while ($row = mysqli_fetch_assoc($result)) {
   
    $voterStatuses[] = [
        'id' => $row['id'],
        'voter_id' => $row['voter_id'],
        'election_id' => $row['election_id'],
                'biometric_failure_count' => $row['biometric_failure_count'],
        'online' => $row['online'],
        'otp_status' => $row['otp_status'],
           'otp_failure_count' => $row['otp_failure_count'],
        'biometric_status' => $row['biometric_status'],
        'voting_status' => $row['voting_status']
    ];
}


echo json_encode($voterStatuses);

mysqli_close($conn);
?>
