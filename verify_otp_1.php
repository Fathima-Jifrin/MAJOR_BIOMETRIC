<?php
session_start();

require 'config.php' ;


if (!isset($_SESSION['otp'])) {
    echo "OTP not found. Please request OTP again.";
    exit();
}

$vm = $_SESSION['vm'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];

    
    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        $aadhaar_number1 = $_SESSION['aadhaar_number'];
        $_SESSION['aadhaar_number1'] = $aadhaar_number1;

              $sql = "UPDATE online_voting_requests SET otp_status = 'verified' WHERE aadhaar_number = ?";

            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $aadhaar_number1);

                
                if (mysqli_stmt_execute($stmt)) {


                }else {
                    echo "Error executing query: " . mysqli_error($conn);
                }

                
                mysqli_stmt_close($stmt);
            } else {
                echo "Error preparing statement: " . mysqli_error($conn);
            }



        if ($vm === 'biometric') {
         
                    echo "
                    <script>
                        alert('OTP verification successfully completed!');
                        window.location.href = 'biometric_intruction.php';
                    </script>
                    ";
                
        } elseif ($vm === 'otp') {
            echo "
            <script>
                alert('OTP verification successfully completed!');
                window.location.href = 'voting/voting_instruction.php';
            </script>
            ";
        }
    } else {
        
        $sql = "UPDATE online_voting_requests SET otp_failure_count = otp_failure_count + 1 WHERE aadhaar_number = ?";

        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $aadhaar_number1);

            
            if (mysqli_stmt_execute($stmt)) {
                echo "
                <script>
                    alert('Invalid OTP. Please try again.');
                    window.location.href = 'verify_otp_1.php';
                </script>
                ";
            } else {
                echo "Error executing query: " . mysqli_error($conn);
            }

            
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify OTP - Election Commission of India</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    
    <header>
        <div class="header-content">
            <h1>Election Commission of India</h1>
            <p>Ensuring Free and Fair Elections</p>
        </div>
    </header>

    
    <main>
        <div class="verification-container">
            <h2>Verify OTP</h2>
            <p>Please enter the OTP sent to your mobile number to verify your identity.</p>
            <form action="" method="POST">
                <label for="otp">Enter OTP:</label>
                <input type="text" name="otp" id="otp" required placeholder="Enter OTP" maxlength="6" pattern="\d{6}">
                <button type="submit">Verify OTP</button>
            </form>
        </div>
    </main>

    
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 Election Commission of India</p>
            <p>All rights reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </footer>
</body>
</html>
<style>


* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
}

a {
    text-decoration: none;
    color: #2a6bf9;
}

a:hover {
    text-decoration: underline;
}


header {
    background-color: #003366;
    color: white;
    padding: 20px;
    text-align: center;
}

header h1 {
    font-size: 36px;
}

header p {
    font-size: 18px;
}


main {
    padding: 40px 20px;
    max-width: 600px;
    margin: 0 auto;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin-top: 30px;
}

.verification-container {
    margin-top: 20px;
    text-align: center;
}

.verification-container h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #003366;
}

.verification-container p {
    font-size: 16px;
    color: #555;
}

form {
    margin-top: 20px;
}

label {
    display: block;
    font-size: 16px;
    margin-bottom: 10px;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    margin-top: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    outline: none;
}

input[type="text"]:focus {
    border-color: #00509e;
}

button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #003366;
    color: white;
    border: none;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #00509e;
}


footer {
    background-color: #003366;
    color: white;
    padding: 20px;
    text-align: center;
    margin-top: 30px;
}

footer p {
    font-size: 14px;
}

footer a {
    color: white;
}

footer a:hover {
    text-decoration: underline;
}

</style>