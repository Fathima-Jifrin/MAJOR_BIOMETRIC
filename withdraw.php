<?php

require 'config.php' ;


$election = null;
$update_message = "";


$sql = "SELECT id, election_name FROM elections WHERE online_voting = 'inactive'"; 
$result = $conn->query($sql);


if (isset($_POST['submit'])) {
    $election_id = $_POST['election_id'];
    $status = $_POST['status'];
    $new_date = isset($_POST['new_election_date']) ? $_POST['new_election_date'] : null;

 
    $check_sql = "SELECT * FROM elections WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $election = $result->fetch_assoc();

        
        if ($status === 'postponed' && $new_date) {
            $update_sql = "UPDATE elections SET online_voting = ?, election_date = ? WHERE id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("ssi", $status, $new_date, $election_id);
        } else {
            $update_sql = "UPDATE elections SET online_voting = ? WHERE id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("si", $status, $election_id);
        }

        if ($stmt_update->execute()) {
            $update_message = "Election status updated successfully!";
        } else {
            $update_message = "Error updating election status: " . $stmt_update->error;
        }
    } else {
        $update_message = "Election with ID " . htmlspecialchars($election_id) . " not found.";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw, Cancel or Postpone Election</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <header>
        <div class="header-container">
            <img src="logo.png" alt="Election Commission of India Logo" class="logo">
            <h1>Election Commission of India</h1>
        </div>
    </header>
<button onclick="window.location.href='electdash.html';" class="p"><-Back</button>

    <div class="container">
        <h2>Withdraw, Cancel or Postpone Election</h2>

        
        <?php if ($update_message): ?>
            <div class="message"><?php echo $update_message; ?></div>

        <?php endif; ?>

        
        <div class="election-buttons">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<form action='' method='POST' style='display: inline-block; margin: 10px;'>";
                    echo "<button type='submit' name='select_election' value='" . $row['id'] . "' class='election-btn'>" . $row['election_name'] . "</button>";
                    echo "</form>";
                }
            } else {
                echo "<p>No elections are currently available for withdrawal or postponement.</p>";
            }
            ?>
        </div>

        
        <?php
        if (isset($_POST['select_election'])) {
            $selected_election_id = $_POST['select_election'];

            
            $conn = new mysqli($host, $user, $pass, $db);
            $election_query = "SELECT * FROM elections WHERE id = ?";
            $stmt = $conn->prepare($election_query);
            $stmt->bind_param("i", $selected_election_id);
            $stmt->execute();
            $election_result = $stmt->get_result();

            if ($election_result->num_rows > 0) {
                $election = $election_result->fetch_assoc();
                ?>

                
                <h2>Update Election Status</h2>
                <form action="" method="POST">
                    <input type="hidden" name="election_id" value="<?php echo $election['id']; ?>">

                    <div class="form-row">
                        <label for="election_name">Election Name:</label>
                        <input type="text" id="election_name" name="election_name" value="<?php echo $election['election_name']; ?>" readonly>

                        <label for="election_date">Election Date:</label>
                        <input type="date" id="election_date" name="election_date" value="<?php echo $election['election_date']; ?>" readonly>
                    </div>

                    <div class="form-row">
                        <label for="new_election_date">New Election Date (if postponed):</label>
                        <input type="date" id="new_election_date" name="new_election_date" value="<?php echo $election['election_date']; ?>">

                        <label for="status">Select New Status:</label>
                        <select id="status" name="status" required>
                            <option value="cancelled">Cancelled</option>
                            <option value="postponed">Postponed</option>
                        </select>
                    </div>

                    <button type="submit" name="submit" class="submit-btn">Update Status</button>
                </form>

                <?php
            } else {
                echo "Election not found!";
            }

            
            $conn->close();
        }
        ?>
    </div>

    
    <footer>
        <p>&copy; 2025 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>

<style>

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
}

header {
    background-color: #003366;
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
}

header .header-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

header .logo {
    width: 50px;
    margin-right: 20px;
}

header h1 {
    font-size: 24px;
}

.container {
    background-color: white;
    padding: 30px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    width: 600px;
    margin: 20px auto;
    border-radius: 8px;
}
.p{
    margin:15px;
    width:100px;
    height:50px;
    padding:5px;
    border-radius:5px;
    color:white;
    background-color:darkblue;
    text-size:20px;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 15px;
}

.form-row label {
    margin-bottom: 8px;
    font-size: 14px;
    color: #333;
    width: 48%;
}

.form-row input, .form-row select {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 48%;
}

button.submit-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 12px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
}

button.submit-btn:hover {
    background-color: #45a049;
}

.message {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.election-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.election-btn {
    padding: 20px;
    font-size: 18px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 10px;
    width: 200px;
    text-align: center;
}

.election-btn:hover {
    background-color: #0056b3;
}

footer {
    background-color: #003366;
    color: white;
    padding: 10px;
    text-align: center;
    position: fixed;
    width: 100%;
    bottom: 0;
}
</style>
