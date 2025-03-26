<?php
session_start();


if (!isset($_SESSION['otp'])) {
    echo "<script>alert('OTP session expired. Please login again.'); window.location.href='adminlog.html';</script>";
    exit();
}


$test = $_SESSION['otp'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $test) {
        header("Location: loading1.php");
        exit();
    } else {
        echo "<script>alert('Invalid OTP!'); window.location.href='verify_otp2.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
        
          body {font-family: Arial, sans-serif;background-color: #f4f4f4;margin-top: 0;padding: 0;display: flex;justify-content: center;align-items: center;flex-direction: column;}header {background-color: darkblue;color: white;text-align: center;width: 100%;padding: 10px;}.logo {width: 50px; /* Adjust logo size */vertical-align: middle;margin-right: 10px;}h2 {color: #333;margin-bottom: 40px;}form {background: white;padding: 20px;border-radius: 8px;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);width: 300px;text-align: center;}label {display: block;margin-bottom: 10px;font-weight: bold;}input[type="text"] {width: 100%;padding: 10px;margin-bottom: 20px;border: 1px solid #ccc;border-radius: 4px;}button {background-color: #FF9933;color: white;padding: 10px;border: none;border-radius: 5px;font-size: 16px;cursor: pointer;width: 100%;}button:hover {background-color: #e68928;}footer {background-color: #333;color: white;text-align: center;padding: 10px;position: absolute;bottom: 0;width: 100%;}
    </style>
</head>
<body>
    <header>
        <img src="logo.png" alt="Election Commission Logo" class="logo">
        <h1>Election Commission of India</h1>
    </header>

    <h2>Verify OTP</h2>
    <form method="POST">
        <label for="otp">Enter the OTP sent to your registered phone number: <?php echo $test; ?></label><br>
        <input type="text" id="otp" name="otp" required><br><br>
        <button type="submit">Verify OTP</button>
    </form>

    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
