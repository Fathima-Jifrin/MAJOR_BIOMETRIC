<?php
session_start();
require 'config.php';


if (!isset($_GET['election_id'])) {
    die("No election selected.");
}

$election_id = $_GET['election_id'];


$sql_election_name = "SELECT election_name FROM elections WHERE id = ?";
$stmt = $conn->prepare($sql_election_name);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($election_name);
$stmt->fetch();


$sql_candidates = "SELECT id, name FROM candidates WHERE election_id = ?";
$stmt_candidates = $conn->prepare($sql_candidates);
$stmt_candidates->bind_param("i", $election_id);
$stmt_candidates->execute();
$stmt_candidates->store_result();
$stmt_candidates->bind_result($candidate_id, $candidate_name);


$candidates = [];
while ($stmt_candidates->fetch()) {
    $candidates[$candidate_id] = [
        'name' => $candidate_name,
        'votes' => 0
    ];
}

$sql_votes = "SELECT v.encrypted_vote, v.nonce, k.secret_key 
              FROM votes v
              INNER JOIN secret_keys k ON v.id = k.vote_id
              WHERE v.election_id = ?";
$stmt_votes = $conn->prepare($sql_votes);
$stmt_votes->bind_param("i", $election_id);
$stmt_votes->execute();
$stmt_votes->store_result();
$stmt_votes->bind_result($encrypted_vote_base64, $nonce_base64, $secret_key_base64);


while ($stmt_votes->fetch()) {
    $encrypted_vote = base64_decode($encrypted_vote_base64);
    $nonce = base64_decode($nonce_base64);
    $secret_key = base64_decode($secret_key_base64);

 
    $decrypted_vote = sodium_crypto_secretbox_open($encrypted_vote, $nonce, $secret_key);

    if ($decrypted_vote !== false) {
        
        $candidate_id = (int)$decrypted_vote;
        if (isset($candidates[$candidate_id])) {
            $candidates[$candidate_id]['votes'] += 1;
        }
    }
}

$stmt->close();
$stmt_candidates->close();
$stmt_votes->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results for <?php echo $election_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        header {
            background-color: #003366; 
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .result-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .vote-result {
            margin: 10px 0;
        }
        .vote-result span {
            margin-right: 10px;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
            margin-top: 40px;
        }
        .btn-custom {
            background-color: #0056b3;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #003366;
        }
    </style>
</head>
<body>

<header>
    <h1>Election Commission of India</h1>
</header>

<div class="container">
    <div class="result-card">
        <h3>Election Results:<span style="color:red;"><?php echo $election_name; ?></span></h3>
        <?php
        
        foreach ($candidates as $candidate) {
            echo '<div class="vote-result">';
            echo "<span>" . $candidate['name'] . "</span>";
            echo "<span>" . $candidate['votes'] . " votes</span>";
            echo '</div>';
        }
        ?>
    </div>

    <div class="result-card">
        <h3>Vote Distribution Graph</h3>
        <canvas id="votesChart"></canvas>
    </div>
</div>

<footer>
    <p>&copy; 2025 Election Commission of India. All rights reserved.</p>
</footer>

<script>
    const candidates = <?php echo json_encode($candidates); ?>;
    const candidateNames = [];
    const voteCounts = [];
    const colors = [
        '#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF8C33', 
        '#8C33FF', '#33FFF5', '#33FF8C', '#F533FF', '#FFD133'
    ];

    let colorIndex = 0;

    for (let candidateId in candidates) {
        candidateNames.push(candidates[candidateId].name);
        voteCounts.push(candidates[candidateId].votes);
    }

    const ctx = document.getElementById('votesChart').getContext('2d');
    const votesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: candidateNames,
            datasets: [{
                label: 'Votes',
                data: voteCounts,
                backgroundColor: function(context) {
                    const index = context.dataIndex;
                    return colors[index % colors.length];
                },
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>

</body>
</html>
