<?php
session_start();
require 'config.php';

if (isset($_POST['activate'])) {
    $election_id = $_POST['election_id'];
    $activation_datetime = $_POST['activation_datetime'];
    $verification_method = $_POST['verification_method'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    $update_sql = "UPDATE elections SET activation_date = ?, verification_method = ?, online_voting = 'active', election_start_time = ?, election_end_time = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssi", $activation_datetime, $verification_method, $start_time, $end_time, $election_id);
    $stmt->execute();
    $stmt->close();
    
    echo "
    <script>
alert('Election activated successfully!');
window.location.href='conduct_online.php';
exit();
    </script>
    ";
}

if (isset($_POST['deactivate'])) {
    $election_id = $_POST['election_id'];
    
    $update_sql = "UPDATE elections SET online_voting = 'deactivate' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $stmt->close();
    
    echo "
    <script>
alert('Election deactivated successfully!');
window.location.href='conduct_online.php';
exit();
    </script>
    ";
}


$election_id = $_GET['id'];
$sql = "SELECT * FROM elections WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Election Activation</title>
</head>
<body>
    <header>
            <img src="../logo.png" alt="Logo" class="logo" width="100" height="100">
            <h1>Election Commission</h1>
            <p>Voter Verification Portal</p>
        </header>
    <div class="container">
        <h2>Set Activation for <?php echo $election['election_name']; ?></h2>
        <form method="post">
            <input type="hidden" name="election_id" value="<?php echo $election['id']; ?>">
            
            <label for="activation_datetime">Activation Date & Time:</label>
            <input type="date" name="activation_datetime" value="<?php echo $election['election_date']; ?>" required><br>
            
            <label for="start_time">Election Start Time:</label>
            <input type="time" name="start_time" value="<?php echo $election['election_start_time']; ?>" required><br>
            
            <label for="end_time">Election End Time:</label>
            <input type="time" name="end_time" value="<?php echo $election['election_end_time']; ?>" required><br>
            
            <label for="verification_method">Verification Method:</label>
            <select name="verification_method" required>
                <option value="otp" <?php echo ($election['verification_method'] == 'otp') ? 'selected' : ''; ?>>OTP</option>
                <option value="biometric" <?php echo ($election['verification_method'] == 'biometric') ? 'selected' : ''; ?>>Biometric</option>
            </select><br>
            
            <button type="submit" name="activate">Activate Election</button>
            <button type="submit" name="deactivate" style="background-color: red;">Deactivate Election</button>
        </form>
    </div>
    <div class="footer">&copy; 2025 Election Commission of India. All rights reserved.</div>
</body>
</html>

<style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
}

.container {
    width: 60%;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


.footer {
    background-color: #003366;
    color: white;
    text-align: center;
    padding: 15px;
    font-size: 20px;
    font-weight: bold;
}

header {
    text-align: center;
    background-color: darkblue;
    color: white;
    padding: 10px 0;
    margin-bottom: 20px;
    position: relative;
}


form {
    margin-top: 20px;
}

label {
    font-size: 16px;
    margin-bottom: 8px;
    display: block;
}

input[type="date"],
input[type="time"],
select {
    width: 100%;
    padding: 8px;
    margin-bottom: 20px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 10px;
}

button:hover {
    background-color: #218838;
}

button:focus {
    outline: none;
}

button[style="background-color: red;"]:hover {
    background-color: darkred;
}

h2 {
    color: #333;
    font-size: 22px;
    margin-bottom: 20px;
}

footer {
    margin-top: 20px;
}

footer span {
    font-size: 14px;
    color: #666;
}
</style>
