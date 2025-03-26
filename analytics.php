<?php
if (!isset($_GET['election_id'])) {
    die("Invalid election selection.");
}
$election_id = intval($_GET['election_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        canvas {
            max-width: 700px;
            margin: auto;
        }
    </style>
</head>
<body>
    <h2>Live Election Analytics</h2>
    <canvas id="voteChart"></canvas>
    
    <script>
        let voteChart;
        
        function fetchVotes() {
            fetch('fetch_votes.php?election_id=<?= $election_id ?>')
                .then(response => response.json())
                .then(data => {
                    updateChart(data);
                });
        }
        
        function updateChart(data) {
            if (!voteChart) {
                const ctx = document.getElementById('voteChart').getContext('2d');
                voteChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.timestamps,
                        datasets: [{
                            label: 'Votes Cast',
                            data: data.votes,
                            borderColor: 'blue',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: 'Time' } },
                            y: { title: { display: true, text: 'Votes' }, beginAtZero: true }
                        }
                    }
                });
            } else {
                voteChart.data.labels = data.timestamps;
                voteChart.data.datasets[0].data = data.votes;
                voteChart.update();
            }
        }
        
        setInterval(fetchVotes, 5000); 
    </script>
</body>
</html>
