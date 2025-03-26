<?php
session_start();
require 'config.php';
$sql_latest = "
    SELECT e.election_name, e.id 
    FROM elections e
    JOIN election_status es ON e.id = es.election_id
    WHERE es.is_published = 1
    ORDER BY e.id DESC  -- Sorting by 'id' to get the latest published election
    LIMIT 1
";

$result_latest = $conn->query($sql_latest);

$latest_election = null;
if ($result_latest->num_rows > 0) {
    $latest_election = $result_latest->fetch_assoc();
}


$sql_old_elections = "
    SELECT e.election_name, e.id 
    FROM elections e
    JOIN election_status es ON e.id = es.election_id
    WHERE es.is_published = 1
    ORDER BY e.id DESC
";
$result_old = $conn->query($sql_old_elections);


if ($latest_election) {
    $election_id = $latest_election['id'];
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
    <style>
       
    </style>
</head>
<body>

<header>
    <h1>Election Results</h1>
    <h2>Live Updates on Election Results</h2>
</header>

<div class="container">
    <?php if ($latest_election): ?>
        <div class="result-card">
            <h3>Latest Election Result For: <span><?php echo $latest_election['election_name']; ?></span></h3>

            <?php
            
            foreach ($candidates as $candidate) {
                echo '<div class="vote-result">';
                echo "<span>" . $candidate['name'] . "</span>";
                echo "<span>" . $candidate['votes'] . " votes</span>";
                echo '</div>';
            }
            ?>

            <div class="canvas-container">
                <h3>Vote Distribution Graph</h3>
                <canvas id="votesChart"></canvas>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No published results found.</div>
    <?php endif; ?>

    <h3>Previously Published Elections</h3>
    <div class="election-carousel">
        <?php if ($result_old->num_rows > 0): ?>
            <?php while ($old_election = $result_old->fetch_assoc()): ?>
                <?php if ($old_election['id'] != $latest_election['id']): ?>
                    <div class="election-item">
                        <h5><?php echo $old_election['election_name']; ?></h5>
                        <form action="view_old_results.php" method="GET">
                            <input type="hidden" name="election_id" value="<?php echo $old_election['id']; ?>">
                            <button type="submit" class="btn btn-primary">View Result</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning">No previous elections found.</div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 Election Commission of India. All rights reserved.</p>
</footer>

<script>
    const candidates = <?php echo json_encode($candidates); ?>;
    const candidateNames = [];
    const voteCounts = [];
    const colors = ['#ff7043', '#ff8a65', '#ffab91', '#f48fb1', '#ce93d8', '#9fa8da'];

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
                backgroundColor: colors,
                borderColor: colors.map(c => c.replace(')', ', 0.8)').replace('rgb', 'rgba')),
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
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            padding-top: 20px;
        }

        header {
            background-color: darkblue;
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

        button {
            font-size: 1rem;
            font-weight: 600;
            padding: 10px 20px;
            color: white;
            background-color: #ff7043;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #d84f24;
        }

        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .election-carousel {
            display: flex;
            overflow-x: auto;
            padding: 20px 0;
            gap: 15px;
        }

        .election-item {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex: 0 0 auto;
            width: 250px;
        }

        .election-item h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .election-item button {
            font-size: 1rem;
            width: 100%;
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
