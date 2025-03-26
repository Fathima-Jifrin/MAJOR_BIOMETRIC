<?php
session_start();


require 'config.php' ;

function generateApplicationID() {
    return substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz", 11)), 0, 11);
}

$full_name = $_POST['full_name'];
$surname = $_POST['surname'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$place_of_birth = $_POST['place_of_birth'];
$current_address = $_POST['current_address'];
$permanent_address = $_POST['permanent_address'];
$state = $_POST['state'];
$district = $_POST['district'];
$pincode = $_POST['pincode'];
$constituency = $_POST['constituency'];
$relation_type = $_POST['relation_type'];
$relation_name = $_POST['relation_name'];
$declaration_date = $_POST['declaration_date'];
$applicant_signature = $_POST['applicant_signature'];
$aadhaar_number = $_POST['aadhaar'];
$voter_id = $_POST['voter_id'];

$application_id = generateApplicationID();

$sql = "INSERT INTO voter_id (application_id, full_name,  date_of_birth ,registration_date, gender, place_of_birth, current_address, permanent_address, state, district, pincode, constituency, relation_type, relation_name,father_place, aadhaar_number,rvoter_id)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssssssss", $application_id, $full_name, $dob,$declaration_date, $gender, $place_of_birth,$current_address, $permanent_address, $state, $district, $pincode, $constituency, $relation_type, $relation_name,  $applicant_signature, $aadhaar_number,$voter_id);

if ($stmt->execute()) {
    $update_sql = "UPDATE voter_registration SET astatus = 'submitted' WHERE aadhaar_number = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("s", $aadhaar_number);
    $update_stmt->execute();
    $update_stmt->close();

    $email_sql = "SELECT email FROM voter_registration WHERE aadhaar_number = ?";
    $email_stmt = $conn->prepare($email_sql);
    $email_stmt->bind_param("s", $aadhaar_number);
    $email_stmt->execute();
    $result = $email_stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $email = $row['email'];

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

            $mail->setFrom('your_email@gmail.com', 'Election Commission of India'); 
            $mail->addAddress($email, $full_name);
            $mail->isHTML(true);

            $mail->Subject = 'Voter Application Submitted Successfully';
            $mail->Body = 'Dear ' . htmlspecialchars($full_name) . ',<br><br>' .
                          'Your application has been submitted successfully! Your Application ID is: ' . htmlspecialchars($application_id) . '<br><br>' .
                          'Your application is under the review of election officer@ '.htmlspecialchars($constituency ).'<br><br>' .
                          'Thank you for registering.';

            $mail->send();
            echo "<script>alert('Registration submitted successfully! An email has been sent to: $email'); window.location.href='dashboard.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Email could not be sent. Error: " . $mail->ErrorInfo . "'); window.location.href='registration.php';</script>";
        }
    } else {
        echo "<script>alert('No email found for Aadhaar number: $aadhaar_number'); window.location.href='logut.php';</script>";
    }

    $email_stmt->close();
} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='preview.php';</script>";
}

$stmt->close();
$conn->close();
?>
