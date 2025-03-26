<?php
session_start();

require 'config.php' ;

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access your dashboard.'); window.location.href='adminlog.html';</script>";
    exit();
}


if (isset($_GET['verify_id'])) {
    $id = $_GET['verify_id'];
    $update_query = "UPDATE candidates SET verified = 'Verified' WHERE id = $id";
    $conn->query($update_query);
    header("Location:vcandidate.php");
}


if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $delete_query = "DELETE FROM candidates WHERE id = $id";
    $conn->query($delete_query);
    header("Location:vcandidate.php");
}

    $election_id = intval($_GET['id']);

$query = "SELECT * FROM candidates where election_id='$election_id' AND verified='Pending'";
$result = $conn->query($query);
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Management Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <header class="header">
        <div class="logo">
            <img src="logo.png" alt="ECI Logo" width="50">
        </div>
        <div class="title">
            <h1>Election Commission of India</h1>
            <h2>Candidate Management Dashboard</h2>
        </div>
    </header>
    <div class="back-button-container">
        <a href="vcandidate.php" class="back-button">← Back</a>
    </div>
    
    <div class="container">
        <h2>Manage Candidates</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Party</th>
                    <th>Party Type</th>
                    <th>Aadhaar</th>
                    <th>Assets</th>
                    <th>Cases</th>
                    <th>Arms Surrendered</th>
                    <th>Image</th>
                    <th>Symbol</th>
                    <th>Declaration</th>
                    <th>Signature</th>
                    <th>Address</th>
                    <th>District</th>
                    <th>State</th>
                    <th>Constituency</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Age</th>
                    <th>Election ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['party']; ?></td>
                    <td><?php echo $row['party_type']; ?></td>
                    <td><?php echo $row['aadhaar_number']; ?></td>
                    <td><?php echo $row['assets']; ?></td>
                    <td><?php echo $row['police_cases']; ?></td>
                    <td><?php echo $row['arms_surrendered']; ?></td>
                    <td><img src="<?php echo $row['candidate_image']; ?>" alt="Candidate Image" class="candidate-image"></td>
                    <td><img src="<?php echo $row['symbol']; ?>" alt="Party Symbol" class="party-symbol"></td>
                    <td><a href="<?php echo $row['declaration']; ?>" target="_blank">View</a></td>
                    <td><img src="<?php echo $row['signature']; ?>" alt="Signature" class="signature"></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['district']; ?></td>
                    <td><?php echo $row['state']; ?></td>
                    <td><?php echo $row['constituency']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['date_of_birth']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['election_id_number']; ?></td>
                    <td><?php echo $row['verified']; ?></td>
                    <td>
                        <a href="viewcandidate.php?verify_id=<?php echo $row['id']; ?>" class="btn verify">Verify</a>
                        <a href="viewcandidate.php?delete_id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Election Commission of India. All Rights Reserved.</p>
    </footer>
</body>
</html>
<style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}


.header {
    background-color: #003366;
    color: white;
    padding: 20px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.header .logo img {
    width: 50px;
    margin-right: 20px;
}

.header h1 {
    font-size: 2.5em;
    margin: 0;
}

.header h2 {
    font-size: 1.5em;
    margin: 0;
    font-weight: lighter;
}


.container {
    margin: 20px;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.container h2 {
    text-align: center;
    margin-bottom: 20px;
}
.back-button-container {
            margin-top: 30px; 
            padding-left: 20px; 
        }

        .back-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #004494;
        }
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table th, table td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
}

table th {
    background-color: #003366;
    color: white;
    font-size: 1em;
}

table td {
    background-color: #f9f9f9;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}


.candidate-image, .party-symbol, .signature {
    width: 50px;
    height: 50px;
    object-fit: cover;
}


.btn {
    display: inline-block;
    padding: 10px 15px;
    margin: 5px;
    border-radius: 5px;
    color: white;
    text-decoration: none;
}

.btn.verify {
    background-color: #28a745;
}

.btn.verify:hover {
    background-color: #218838;
}

.btn.delete {
    background-color: #dc3545;
}

.btn.delete:hover {
    background-color: #c82333;
}


.footer {
    background-color: #003366;
    color: white;
    padding: 10px 0;
    text-align: center;
    position: fixed;
    width: 100%;
    bottom: 0;
}

.footer p {
    margin: 0;
}


@media screen and (max-width: 768px) {
    table th, table td {
        padding: 8px;
        font-size: 0.9em;
    }

    .header h1 {
        font-size: 2em;
    }

    .header h2 {
        font-size: 1.2em;
    }

    .container {
        margin: 10px;
        padding: 10px;
    }
}

</style>