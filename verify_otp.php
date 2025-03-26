<?php
session_start();

require 'config.php' ;

if (!isset($_SESSION['otp'])) {
    echo "<script>alert('No OTP generated. Please register again.'); window.location.href='reg.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredOTP = $_POST['otp'];

    if ($enteredOTP == $_SESSION['otp']) {
        
        $aadhaarNumber = $_SESSION['aadhaar_number'];
        $fullName = $_SESSION['full_name'];
        $dob = $_SESSION['date_of_birth'];
        $phoneNumber = $_SESSION['phoneNumber'];
        $email = $_SESSION['email'];
        $passwordHash = $_SESSION['password'];

        $stmt = $conn->prepare("INSERT INTO voter_registration (aadhaar_number, full_name, date_of_birth, phone_number, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $aadhaarNumber, $fullName, $dob, $phoneNumber, $email, $passwordHash);

        if ($stmt->execute()) {
            
            unset($_SESSION['otp']);
            unset($_SESSION['aadhaar_number']);
            unset($_SESSION['phoneNumber']);
            unset($_SESSION['full_name']);
            unset($_SESSION['date_of_birth']);
            unset($_SESSION['email']);
            unset($_SESSION['password']);

            echo "<script>alert('Registration successful! You can now login.'); window.location.href='log.html';</script>";
        } else {
            echo "<script>alert('Database error. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Election Commission of India</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Election Commission of India Logo">
        </div>
        <h1>Election Commission of India</h1>
    </header>

    <div class="container">
        <form action="" method="POST" class="otp-form">
            <h2>Enter OTP <?php echo $_SESSION['otp'] ?></h2>
            <p>An OTP has been sent to your registered mobile number.</p>

            <label for="otp">OTP:</label>
            <input type="text" id="otp" name="otp" pattern="\d{6}" required title="OTP must be 6 digits.">

            <button type="submit" class="btn-primary">Verify OTP</button>
        </form>
        <p>Didn’t receive an OTP? <a href="reg.php">Register again</a>.</p>
    </div>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    line-height: 1.6;
    background-color: #f4f4f4;
}

header {
    background-color: darkblue;
    color: #fff;
    padding: 20px;
    text-align: center;
}

.logo img {
    width: 60px; 
}

.container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

h1, h2 {
    text-align: center;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    text-align: center;
}

.btn-primary {
    background-color: darkblue;
    color: white;
    padding: 12px 25px;
    text-decoration: none;
    font-size: 1.1rem;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    width: 100%;
}

.btn-primary:hover {
    background-color: #e68928;
}

footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 20px 0;
    position: relative;
    bottom: 0;
    width: 100%;
}

p {
    text-align: center;
    margin-top: 20px;
}

a {
    color: #FF9933;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>