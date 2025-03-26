<?php
session_start();

require 'config.php';

$election_id = $_SESSION['election_id'];
$voter_id = $_SESSION['voter_id'];


$sql_email = "SELECT email, full_name FROM voter_registration WHERE id = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("i", $voter_id); 
$stmt_email->execute();
$stmt_email->bind_result($email, $name);  
$stmt_email->fetch();  


if (!$email) {
    echo "Voter email not found!";
    exit;  
}

$stmt_email->close();


$sql = "SELECT election_id, name, party, symbol, id FROM candidates WHERE election_id = '$election_id'";
$result = $conn->query($sql);





function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP']; 
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR']; 
    }
}

$user_ip = getUserIP();

$os_info = php_uname();

function getLocationData($ip) {
    $token = '6c7f2428df253f'; 
    $url = "http://ipinfo.io/{$ip}/json?token={$token}";

    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    $response = curl_exec($ch); 
    curl_close($ch); 

    return json_decode($response, true);  
}


$location_info = getLocationData($user_ip);


$city = $location_info['city'] ?? 'Unknown';
$region = $location_info['region'] ?? 'Unknown';
$country = $location_info['country'] ?? 'Unknown';


function sendVoteSuccessEmail($email, $name, $vote_id, $user_ip, $os_info, $city, $region, $country) {
    include_once("../class.phpmailer.php");
include_once("../class.smtp.php");
    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ebinbenny1709@gmail.com';  
        $mail->Password = 'kouiproacwnesmpg';  
        $mail->SMTPSecure = 'tls';  
        $mail->Port = 587;  

        
        $mail->setFrom('your-email@gmail.com', 'Election Commission of India');
        $mail->addAddress($email, $name);  

        
        $mail->isHTML(true);
        $mail->Subject = 'Vote Successful Confirmation';
        $mail->Body = 'Dear ' . $name . ',<br><br>' .
            'Your vote has been successfully cast. Below are the details:<br><br>' .
            'Vote ID: <strong>' . $vote_id . '</strong><br>' .
            'IP Address: <strong>' . $user_ip . '</strong><br>' .
            'Operating System: <strong>' . $os_info . '</strong><br>' .
            'Location: <strong>' . $city . ', ' . $region . ', ' . $country . '</strong><br><br>' .
            'Thank you for participating in the Election Commission of India.';

        
        $mail->send();
    } catch (Exception $e) {
        echo 'Vote confirmation email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $candidate_id = $_POST['candidate_id'];

  
    $secret_key = sodium_crypto_secretbox_keygen();
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);


    $encrypted_vote = sodium_crypto_secretbox((string)$candidate_id, $nonce, $secret_key);

   
    $encrypted_vote_base64 = base64_encode($encrypted_vote);
    $nonce_base64 = base64_encode($nonce);

 
    $stmt = $conn->prepare("INSERT INTO votes (election_id, encrypted_vote, nonce, vote_time) 
                            VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $election_id, $encrypted_vote_base64, $nonce_base64);
    $stmt->execute();
    $vote_id = $stmt->insert_id;  

    $secret_key_base64 = base64_encode($secret_key);  
    $stmt_secret = $conn->prepare("INSERT INTO secret_keys (vote_id, secret_key) VALUES (?, ?)");
    $stmt_secret->bind_param("is", $vote_id, $secret_key_base64);
    $stmt_secret->execute();

  
    $stmt->close();
    $stmt_secret->close();

    $stmt_update = $conn->prepare("UPDATE online_voting_requests SET vote_state = 'done' WHERE voter_id = ?");
    $stmt_update->bind_param("i", $voter_id);
    $stmt_update->execute();
    $stmt_update->close();

    sendVoteSuccessEmail($email, $name, $vote_id, $user_ip, $os_info, $city, $region, $country);

    echo "<script>alert('Your vote has been successfully cast!');</script>";

    
    echo "<script>
        setTimeout(function() {
            window.location.href = 'vote_success.php';
        }, 4000);
    </script>";
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
            background-image: repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0.05) 2px, transparent 2px, transparent 10px),
                              repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0.05) 2px, transparent 2px, transparent 10px);
            background-size: 100px 100px;
        }

        body::before {
            content: "<?php echo $epic_value; ?>"; 
            font-size: 50px;
            color: rgba(0, 0, 0, 0.1); 
            font-weight: bold;
            font-family: Arial, sans-serif; 
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            text-align: center;
            z-index: -1;
            opacity: 0.2;
            text-transform: uppercase;
            letter-spacing: 2px;
            white-space: nowrap; 
            display: flex;
            justify-content: center;
            align-items: center;
            transform: translate(-50%, -50%);
        }

        .voting-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 700px;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        .vote-button {
            background-color: #181feb;
            height: 70px;
            width: 170px;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 35px;
            cursor: pointer;
            font-size: 16px;
        }

        .vote-button:hover {
            background-color: #45a049;
        }

        img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
            position: relative;
        }

        .modal-content img {
            width: 120px;
            height: 120px;
            margin-bottom: 15px;
        }

        .modal-buttons {
            margin-top: 15px;
        }

        .modal-buttons button {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin: 5px;
        }

        .confirm-button {
            background-color: #28a745;
            color: white;
        }

        .cancel-button {
            background-color: #dc3545;
            color: white;
        }

    </style>
</head>
<body>
    <div class="voting-container">
        <h2>Vote for Your Candidate</h2>
        <table>
            <thead>
                <tr>
                    <th>Party Symbol</th>
                    <th>Candidate Name</th>
                    <th>Cast Vote</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='../" . $row['symbol'] . "' alt='" . $row['party'] . " Symbol'></td>";
                        echo "<td>" . $row['name'] . " (" . $row['party'] . ")</td>";
                        echo "<td><button class='vote-button' onclick='openModal(\"" . $row['name'] . "\", \"../" . $row['symbol'] . "\", \"" . $row['id'] . "\")'>Vote</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No candidates available.</td></tr>";
                }
                ?>
                 <tr>
                    <td><img src="nota-symbol.png" alt="NOTA Symbol"></td>
                    <td>None of the Above (NOTA)</td>
                    <td><button class="vote-button" onclick="openModal('NOTA', 'nota-symbol.png', '')">Vote</button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="voteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Your Vote</h3>
            <img id="candidateImage" src="" alt="Candidate Symbol">
            <p id="candidateName"></p>
            <div class="modal-buttons">
                <form method="POST" action="">
                    <input type="hidden" id="candidateId" name="candidate_id">
                    <button type="submit" class="confirm-button">Confirm</button>
                    <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(name, symbol, candidateId) {
            document.getElementById('candidateName').innerText = "You are voting for " + name;
            document.getElementById('candidateImage').src = symbol;
            document.getElementById('candidateId').value = candidateId;
            document.getElementById('voteModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('voteModal').style.display = 'none';
        }
    </script>

</body>
</html>
