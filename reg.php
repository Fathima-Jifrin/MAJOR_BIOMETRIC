<?php
session_start();

require 'config.php' ;


function sendOTP($phoneNumber, $otp) {
    $API = "c8cd63e1bf13c5016881652983fb615a";
    $PHONE = $phoneNumber;
    $OTP = $otp;
    $URL = "https://sms.renflair.in/V1.php?API=$API&PHONE=$PHONE&OTP=$OTP";

    $curl = curl_init($URL);
    curl_setopt($curl, CURLOPT_URL, $URL);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    
    error_log("OTP API Response: " . $resp);

    
    return $resp;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aadhaarNumber = $_POST['aadhaar'];
    $fullName = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit;
    }

    
    $stmt = $conn->prepare("SELECT * FROM voter_registration WHERE aadhaar_number = ?");
    $stmt->bind_param("s", $aadhaarNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Aadhaar number already registered.');</script>";
    } else {
        
        $stmt = $conn->prepare("SELECT phone_number, full_name, date_of_birth FROM aadhaar_data WHERE aadhaar_number = ?");
        $stmt->bind_param("s", $aadhaarNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $phoneNumber = $row['phone_number'];
            $aadhaarName = $row['full_name'];
            $dob = $row['date_of_birth'];

            
            if (strcasecmp($aadhaarName, $fullName) !== 0) {
                echo "<script>alert('The name entered does not match the name in the Aadhaar records.');</script>";
                exit;
            }

            
            $otp = rand(100000, 999999);

            
            $otpResponse = sendOTP($phoneNumber, $otp);

            
            if ($otpResponse === false) {
                echo "<script>alert('Failed to send OTP. Please try again later.');</script>";
            } else {
                
                $_SESSION['otp'] = $otp;
                $_SESSION['aadhaar_number'] = $aadhaarNumber;
                $_SESSION['phoneNumber'] = $phoneNumber;
                $_SESSION['full_name'] = $fullName;
                $_SESSION['date_of_birth'] = $dob;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = password_hash($password, PASSWORD_BCRYPT);

                
                header("Location: verify_otp.php");
                exit;
            }
        } else {
            echo "<script>alert('Invalid Aadhaar number.');
            window.location.href='reg.php';</script>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Registration - Election Commission of India</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Election Commission of India Logo">
        </div>
        <h1>Voter Registration</h1>
    </header>
    
    <div class="container">
        <form action="" method="POST" class="registration-form">
            <h2>Register to Vote</h2>

            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="aadhaar">Aadhaar Number:</label>
            <input type="text" id="aadhaar" name="aadhaar" pattern="\d{12}" required title="Aadhaar number must be 12 digits.">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Create Password:</label>
            <input type="password" id="password" name="password" required minlength="8">

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required minlength="8">

            <button type="submit" class="btn-primary">Register</button>
        </form>
        <p>Already have an account? <a href="log.html">Login here</a>.</p>
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
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="date"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
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
