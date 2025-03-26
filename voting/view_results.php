<?php
session_start();
require 'config.php';

if (!isset($_GET['election_id'])) {
    die("No election selected.");
}

$election_id = $_GET['election_id'];


$sql = "SELECT election_name FROM elections WHERE id = '$election_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
 
    $row = $result->fetch_assoc();
    $election_name = $row['election_name'];
}


$sql_candidates = "SELECT id, name FROM candidates WHERE election_id = ?";
$stmt = $conn->prepare($sql_candidates);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($candidate_id, $candidate_name);


$candidates = [];
while ($stmt->fetch()) {
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


$sql_check_status = "SELECT is_published FROM election_status WHERE election_id = ?";
$stmt_check = $conn->prepare($sql_check_status);
$stmt_check->bind_param("i", $election_id);
$stmt_check->execute();
$stmt_check->store_result();
$stmt_check->bind_result($is_published);
$stmt_check->fetch();
$stmt_check->close();


if (isset($_POST['publish_result']) && $is_published != 1) {
    
    $sql_publish_results = "INSERT INTO published_results (election_id, candidate_id, vote_count, published_at)
                            VALUES (?, ?, ?, NOW()) 
                            ON DUPLICATE KEY UPDATE vote_count = VALUES(vote_count), published_at = NOW()";

    
    $stmt_publish = $conn->prepare($sql_publish_results);

    foreach ($candidates as $candidate_id => $candidate) {
        $vote_count = $candidate['votes'];
        $stmt_publish->bind_param("iii", $election_id, $candidate_id, $vote_count);
        $stmt_publish->execute();
    }

   
    if ($stmt_publish->affected_rows > 0) {
       
        $sql_update_status = "INSERT INTO election_status (election_id, is_published, updated_at)
                              VALUES (?, TRUE, NOW())
                              ON DUPLICATE KEY UPDATE is_published = TRUE, updated_at = NOW()";

        $stmt_update_status = $conn->prepare($sql_update_status);
        $stmt_update_status->bind_param("i", $election_id);
        $stmt_update_status->execute();

        if ($stmt_update_status->affected_rows > 0) {
            echo "<div class='alert alert-success'>Election results published successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating election status. Please try again.</div>";
        }

        $stmt_update_status->close();
    } else {
        echo "<div class='alert alert-danger'>Error publishing results. Please try again.</div>";
    }

    $stmt_publish->close();
}

$stmt->close();
$stmt_votes->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<header>
    <h1>Election Commission of India</h1>
    <h2>Election Results</h2>
</header>

<div class="container">
    <div class="result-card">
        <h3>Election Results For: <span><?php echo $election_name; ?></span></h3>

        <?php
        
        foreach ($candidates as $candidate) {
            echo '<div class="vote-result">';
            echo "<span>" . $candidate['name'] . "</span>";
            echo "<span>" . $candidate['votes'] . " votes</span>";
            echo '</div>';
        }
        ?>

        <form method="POST">
            <?php if ($is_published == 1): ?>
                <button type="submit" name="publish_result" class="btn btn-success" disabled>Results Already Published</button>
            <?php else: ?>
                <button type="submit" name="publish_result" class="btn btn-success">Publish Result</button>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($is_published == 1): ?>
        <div class="alert alert-info">Results have been published.</div>
    <?php else: ?>
        <div class="alert alert-warning">Results have not been published yet.</div>
    <?php endif; ?>

    <div class="result-card">
        <h3>Vote Distribution Graph</h3>
        <canvas id="votesChart"></canvas>
    </div>

    <h3>Published Results</h3>
    <?php
    
    $conn = new mysqli($host, $user, $pass, $db);
    $sql_get_published_results = "SELECT c.name, r.vote_count
                                  FROM published_results r
                                  JOIN candidates c ON r.candidate_id = c.id
                                  WHERE r.election_id = ?";
    $stmt_results = $conn->prepare($sql_get_published_results);
    $stmt_results->bind_param("i", $election_id);
    $stmt_results->execute();
    $stmt_results->store_result();
    $stmt_results->bind_result($candidate_name, $vote_count);

    while ($stmt_results->fetch()) {
        echo "<div class='vote-result'>";
        echo "<span>" . $candidate_name . "</span>";
        echo "<span>" . $vote_count . " votes</span>";
        echo "</div>";
    }
    $stmt_results->close();
    $conn->close();
    ?>
</div>

<footer>
    <p>&copy; 2025 Election Commission of India. All rights reserved.</p>
</footer>

<script>
    const candidates = <?php echo json_encode($candidates); ?>;
    const candidateNames = [];
    const voteCounts = [];

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
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
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


<style>


* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f6f9;
    color: #333;
    line-height: 1.6;
    padding-top: 20px;
}


header {
    background-color: #004b87;
    color: white;
    text-align: center;
    padding: 30px 20px;
    border-radius: 8px 8px 0 0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

header h2 {
    font-size: 1.5rem;
    font-weight: 500;
}


.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
}


.result-card {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.result-card h3 {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.vote-result {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 1.1rem;
}

.vote-result:last-child {
    border-bottom: none;
}

.vote-result span {
    font-weight: 600;
}

.vote-result span:first-child {
    color: #004b87;
}

.vote-result span:last-child {
    color: #007bff;
}


button {
    font-size: 1rem;
    font-weight: 600;
    padding: 10px 20px;
    color: white;
    background-color: #28a745;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #218838;
}

button:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}


.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 1.1rem;
}
span{
    color:red;
}

.alert-success {
    background-color: #28a745;
    color: white;
}

.alert-danger {
    background-color: #dc3545;
    color: white;
}

.alert-info {
    background-color: #17a2b8;
    color: white;
}

.alert-warning {
    background-color: #ffc107;
    color: black;
}


.canvas-container {
    margin-top: 30px;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

canvas {
    width: 100% !important;
    height: 400px !important;
}


footer {
    background-color: #004b87;
    color: white;
    text-align: center;
    padding: 20px;
    border-radius: 0 0 8px 8px;
    margin-top: 30px;
    font-size: 1rem;
}

footer p {
    margin: 0;
}


@media (max-width: 768px) {
    header h1 {
        font-size: 2rem;
    }

    header h2 {
        font-size: 1.3rem;
    }

    .result-card h3 {
        font-size: 1.5rem;
    }

    .vote-result {
        font-size: 1rem;
    }

    .vote-result span {
        font-size: 1rem;
    }

    button {
        font-size: 1.2rem;
        padding: 12px 24px;
    }

    canvas {
        height: 300px !important;
    }
}

</style>