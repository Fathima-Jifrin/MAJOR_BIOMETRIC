<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aadhaar = $_POST['aadhaar'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM voter_registration WHERE aadhaar_number = ?");
    $stmt->bind_param("s", $aadhaar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $login_attempts = $row['login_attempts'];

        if ($row['is_locked']) {
            $lock_time = strtotime($row['last_failed_login']);
            $unlock_time = $lock_time + 86400;

            if (time() < $unlock_time) {
                echo "<script>alert('Account locked! Try again after 24 hours.'); window.location.href='login.php';</script>";
                exit;
            } else {
                $stmt = $conn->prepare("UPDATE voter_registration SET is_locked = 0, login_attempts = 0 WHERE id = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
            }
        }

        if (password_verify($password, $row['password'])) {
            $stmt = $conn->prepare("UPDATE voter_registration SET login_attempts = 0 WHERE id = ?");
            $stmt->bind_param("i", $row['id']);
            $stmt->execute();

            $phone_number = $row['phone_number'];
            $otp = rand(100000, 999999); 
            $_SESSION['otp'] = $otp;
            $_SESSION['voter_id'] = $row['id'];
            $_SESSION['phone_number'] = $phone_number;

            sendOTP($otp, $phone_number);
            sendEmail($row['email'], $row['full_name'], $otp);

            header("Location: verify_otp1.php");
            exit;
        } else {
            handleFailedLogin($row, $conn);
        }
    } else {
        echo "<script>alert('Aadhaar number not registered!'); window.location.href='login.php';</script>";
    }

    $stmt->close();
}
$conn->close();

function sendOTP($otp, $phone_number) {
    $API = "c8cd63e1bf13c5016881652983fb615a";
    $URL = "https://sms.renflair.in/V1.php?API=$API&PHONE=$phone_number&OTP=$otp";
    $curl = curl_init($URL);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
    curl_close($curl);
}

function sendEmail($email, $name, $otp) {
    include_once("class.phpmailer.php");
    include_once("class.smtp.php");

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ebinbenny1709@gmail.com';
        $mail->Password = 'kouiproacwnesmpg';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ebinbenny1709@gmail.com', 'Election Commission of India');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'OTP';
        $mail->Body = 'Dear ' . htmlspecialchars($name) . ',<br><br>Your OTP is: ' . htmlspecialchars($otp) . '<br><br>Thank you for your attention.';

        $mail->send();
    } catch (Exception $e) {
        echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
    }
}

function handleFailedLogin($row, $conn) {
    $login_attempts = $row['login_attempts'] + 1;

    if ($login_attempts >= 4) {
        $stmt = $conn->prepare("UPDATE voter_registration SET is_locked = 1, last_failed_login = NOW(), login_attempts = ? WHERE id = ?");
        $stmt->bind_param("ii", $login_attempts, $row['id']);
        $stmt->execute();

        sendEmailAlert($row['email'], $row['full_name'], $login_attempts);
        echo "<script>alert('Account locked due to 3 failed attempts! Please try again after 24 hours.'); window.location.href='login.php';</script>";
    } else {
        $stmt = $conn->prepare("UPDATE voter_registration SET login_attempts = ? WHERE id = ?");
        $stmt->bind_param("ii", $login_attempts, $row['id']);
        $stmt->execute();

        sendEmailAlert($row['email'], $row['full_name'], $login_attempts);
        echo "<script>alert('Incorrect password!'); window.location.href='login.php';</script>";
    }
}

function sendEmailAlert($email, $name, $login_attempts) {
    include_once("class.phpmailer.php");
    include_once("class.smtp.php");

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ebinbenny1709@gmail.com';
        $mail->Password = 'kouiproacwnesmpg';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ebinbenny1709@gmail.com', 'Election Commission of India');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);

        $remaining_attempts = 3 - $login_attempts; 
        $mail->Subject = 'Failed Login Attempt';
        $mail->Body = 'Dear ' . htmlspecialchars($name) . ',<br><br>' .
                      'We noticed a failed login attempt to your account. If this wasn\'t you, please reset your password immediately.<br><br>' .
                      'You have ' . htmlspecialchars($remaining_attempts) . ' attempt(s) left before your account gets locked for 24 hours.<br><br>' .
                      'Thank you for your attention.';

        $mail->send();
    } catch (Exception $e) {
        echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
    }
}
?>
