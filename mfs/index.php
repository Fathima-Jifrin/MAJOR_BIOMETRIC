<?php
require 'config.php';
// Fetch Aadhaar users
$sql = "SELECT id, full_name, aadhaar_number FROM aadhaar_data WHERE index_f IS NOT NULL AND index_f <> ''";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select User for Fingerprint Scan</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #111; color: white; }
        table { width: 80%; margin: auto; border-collapse: collapse; background: #222; }
        th, td { border: 1px solid #555; padding: 10px; text-align: left; color: white; }
        th { background-color: #4CAF50; }
        button { padding: 8px 15px; background: blue; color: white; border: none; cursor: pointer; }
        button:hover { background: darkblue; }
    </style>
</head>
<body>

    <h2>Select a User to Capture Fingerprint</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Aadhaar Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['full_name'] ?></td>
                    <td><?= $user['aadhaar_number'] ?></td>
                    <td><a href="cap.php?aadhaar=<?= $user['aadhaar_number'] ?>"><button>Scan Fingerprint</button></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
