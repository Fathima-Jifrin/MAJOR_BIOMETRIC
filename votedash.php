<?php
session_start();

require 'config.php' ;


if (!isset($_GET['aadhaar_number'])) {
    die("Aadhaar number not found!");
}
$verification_method=$_GET['verification_method'];
                                    $_SESSION['vm']=$verification_method;
$aadhaar_number = $_GET['aadhaar_number'];
 $_SESSION['aadhaar_number'] = $aadhaar_number;


$stmt = $conn->prepare("SELECT phone_number FROM aadhaar_data WHERE aadhaar_number = ?");
$stmt->bind_param("s", $aadhaar_number);
$stmt->execute();
$result = $stmt->get_result();

$phone_number = '';
if ($result->num_rows > 0) {
    $row1 = $result->fetch_assoc();
    $phone_number = $row1['phone_number'];
} else {
    die("No phone number found for the provided Aadhaar number.");
}
$stmt->close();


$stmt = $conn->prepare("SELECT email, full_name FROM voter_registration WHERE aadhaar_number = ?");
$stmt->bind_param("s", $aadhaar_number);
$stmt->execute();
$result = $stmt->get_result();

$email = $name = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['email'];
    $name = $row['full_name'];
}
$stmt->close();


function sendOTP($otp, $phone_number) {


        $API="c8cd63e1bf13c5016881652983fb615a";
$PHONE=$phone_number;
$OTP=$otp;
$URL="https://sms.renflair.in/V1.php?API=$API&PHONE=$PHONE&OTP=$OTP";
$curl=curl_init($URL);
curl_setopt($curl, CURLOPT_URL, $URL);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($curl);
curl_close($curl);
$data = json_decode($resp);
}


function sendEmail($email, $name, $otp) {
    require 'class.phpmailer.php';
    require 'class.smtp.php';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ebinbenny1709@gmail.com';
        $mail->Password = 'kouiproacwnesmpg';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'Election Commission of India');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Aadhaar OTP Verification for Online Vote Casting';
        $mail->Body = "
            Dear " . htmlspecialchars($name) . ",<br><br>
            You are receiving this OTP as part of the Aadhaar verification process for online vote casting.<br><br>
            Your OTP for Aadhaar verification is: <strong>" . htmlspecialchars($otp) . "</strong><br><br>
            Please use this OTP to complete your online voting process.<br><br>
            Thank you for your participation in the democratic process.<br><br>
            Sincerely,<br>
            Election Commission of India";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent: " . $mail->ErrorInfo);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mobile_number'])) {


   
$sql = "UPDATE online_voting_requests SET online = 'active' WHERE aadhaar_number = ?";
    
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        
        mysqli_stmt_bind_param($stmt, "s", $aadhaar_number);
        
        
        if (mysqli_stmt_execute($stmt)) {
        } else {
        }
        
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }


   $mobile_number = $_POST['mobile_number'];

    
    $otp = rand(100000, 999999);

    
    $_SESSION['otp'] = $otp;
    $_SESSION['mobile_number'] = $mobile_number;

    
    sendOTP($otp, $mobile_number);
    sendEmail($email, $name, $otp);

    
    header('Location: verify_otp_1.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Voting Dashboard - Election Commission of India</title>
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
        <div class="dashboard-container">
            <h2>Voter Authentication Phase I</h2>

            
            <div class="verification-step">
                <h3>Step 1: Mobile Number Verification</h3>
                <p>Please enter your mobile number to receive an OTP for verification.</p>
                <form action="" method="post">
                    <label for="mobile_number">Mobile Number:</label>
                    <input type="text" id="mobile_number" value="<?php echo htmlspecialchars($phone_number); ?>" name="mobile_number" required placeholder="Enter your mobile number" pattern="\d{10}" maxlength="10" readonly>
                    <input type="submit" value="Send OTP">
                </form>
            </div>
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
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4;
    color: #333;
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
    max-width: 1000px;
    margin: 0 auto;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.dashboard-container {
    margin: 0 auto;
}

.dashboard-container h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
}

.verification-step {
    margin-top: 30px;
}

form {
    margin-top: 20px;
}

input[type="text"],
input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

input[type="submit"] {
    background-color: #003366;
    color: white;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #00509e;
}


footer {
    background-color: #003366;
    color: white;
    padding: 15px;
    text-align: center;
    margin-top: 30px;
}

footer p {
    font-size: 14px;
}

footer a {
    color: #fff;
}

footer a:hover {
    text-decoration: underline;
}

</style>
