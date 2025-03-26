<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongoing Elections</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        .election-list {
            max-width: 500px;
            margin: auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .election-list a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            text-decoration: none;
            background: #007bff;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Ongoing Elections</h2>
    <div class="election-list">
        <?php
            require 'config.php' ;

        $result = $conn->query("SELECT id, election_name FROM elections WHERE online_voting='active'");
        while ($row = $result->fetch_assoc()) {
            echo "<a href='analytics.php?election_id=" . $row['id'] . "'>" . $row['election_name'] . "</a>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>