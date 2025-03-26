<?php
include 'config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $aadhaar_number = $_POST['aadhaar_number'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $address = $_POST['address'];
    $landmark = $_POST['landmark'];
    $pincode = $_POST['pincode'];
    $state = $_POST['state'];

    $stmt = $conn->prepare("INSERT INTO aadhaar_data (full_name, aadhaar_number, email, phone_number, date_of_birth, address, landmark, pincode, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $full_name, $aadhaar_number, $email, $phone_number, $date_of_birth, $address, $landmark, $pincode, $state);

    if ($stmt->execute()) {
        echo "<script>alert('Data submitted successfully!'); window.location='image.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
