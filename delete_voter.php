<?php
session_start();

require 'config.php';

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access this page.'); window.location.href='adminlog.html';</script>";
    exit();
}


$search = "";
$result = null;

if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT v.full_name, v.EPIC, v.aadhaar_number 
            FROM voter_id v 
            INNER JOIN voter_registration vr ON v.aadhaar_number = vr.aadhaar_number
            WHERE (v.full_name LIKE '%$search%' OR v.EPIC LIKE '%$search%' OR v.aadhaar_number LIKE '%$search%')
            AND vr.astatus = 'approved'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Voter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        header {
            background-color: darkblue;
            color: white;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .search-box {
            margin-bottom: 20px;
        }
        .search-box input {
            padding: 8px;
            width: 300px;
        }
        .search-box button {
            padding: 8px 15px;
            background-color: #0056b3;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-box button:hover {
            background-color: #004494;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #0056b3;
            color: white;
        }
        .delete-button {
            padding: 8px 12px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: darkred;
        }
        .back-button-container {
    margin-top: 20px; 
    margin-left: -94%; 
}

.back-button {
    display: inline-block;
    padding: 10px 15px;
    background-color: #0056b3;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
}

.back-button:hover {
    background-color: #004494;
}
.delete-req-container {
    margin-top: -39px; 
    margin-left: 89.5%;
}

.delete-req {
    display: inline-block;
    padding: 10px 15px;
    background-color: #0056b3;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
}

.delete-req:hover {
    background-color: #004494;
}

footer {
    background-color: #112240;
    text-align: center;
    padding: 10px;
    margin-top: auto;
    position: fixed;
    color: white;
    bottom: 0;
    width: 95%;
}
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="ECI Logo" width="100" height="100">
        </div>
        <h1>Election Commission of India</h1>
    </header>
    <div class="back-button-container">
            <a href="vm.html" class="back-button">← Back</a>
        </div>
    <div class="delete-req-container">
            <a href="delete_request_admin.php" class="delete-req">Delete Requests</a>
        </div>
    <h2>Registered Voters</h2>

    <form method="post" class="search-box">
        <input type="text" name="search" placeholder="Search by Name, Voter ID, or Aadhaar" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Voter ID</th>
                    <th>Aadhaar Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['EPIC']); ?></td>
                        <td><?php echo htmlspecialchars($row['aadhaar_number']); ?></td>
                        <td>
                            <form action="delete_voter_action.php" method="post" onsubmit="return confirm('Are you sure you want to delete this voter?');">
                                <input type="hidden" name="aadhaar_number" value="<?php echo htmlspecialchars($row['aadhaar_number']); ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($_POST['search'])): ?>
        <p>No voters found</p>
    <?php endif; ?>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>

    
</body>
</html>

<?php
$conn->close();
?>
