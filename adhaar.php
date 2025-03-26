<?php
session_start();

require 'config.php';

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access your dashboard.'); window.location.href='adminlog.html';</script>";
    exit();
}


if (isset($_GET['aadhaar_number'])) {
    $encoded_aadhaar = $_GET['aadhaar_number'];
    $aadhaar_number = base64_decode($encoded_aadhaar); 

    $stmt = $conn->prepare("SELECT * FROM aadhaar_data WHERE aadhaar_number = ?");
    $stmt->bind_param("s", $aadhaar_number);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath=$row['image'];
    } else {
        echo "<script>alert('No Aadhaar details found!'); window.location.href='dashboard.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No Aadhaar number provided!'); window.location.href='dashboard.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compact DigiLocker Verified e-Aadhaar</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
                     <img src="digi.png" alt="digi locker">
                     <br>
            <h2>DigiLocker Verified e-Aadhaar</h2>
            <p>
                This document is generated from verified Aadhaar XML obtained from DigiLocker with due user consent and authentication
            </p>
        </header>

        <section class="verification">
            <div>
                <strong>Document type:</strong> e-Aadhaar generated from DigiLocker verified Aadhaar XML
            </div>
            <div class="xml-verified">
                <strong>XML verified</strong>
                <img src="verified-icon.png" alt="Verified Icon">
            </div>
        </section>

        <section class="info-section">
           <div class="photo">
    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="User Photo">
</div>

            <div class="details">
                <div class="row">
                    <strong>Name:</strong> <span><?php echo htmlspecialchars($row['full_name']); ?></span>
                </div>
                <div class="row">
                    <strong>Date of Birth:</strong> <span><?php echo htmlspecialchars($row['date_of_birth']); ?></span>
                </div>
                <div class="row">
                    <strong>Masked Aadhaar No:</strong> <span><?php echo substr(htmlspecialchars($row['aadhaar_number']), 0, 4) . "xxxxxxxx" . substr(htmlspecialchars($row['aadhaar_number']), -4); ?></span>
                </div>
                <div class="row">
                    <strong>Address:</strong> <span><?php echo htmlspecialchars($row['address']); ?></span>
                </div>
                <div class="row">
                    <strong>Landmark:</strong> <span><?php echo htmlspecialchars($row['Landmark']); ?></span>
                </div>
                <div class="row">
                    <strong>Pin Code:</strong> <span><?php echo htmlspecialchars($row['pincode']); ?></span>
                </div>
                <div class="row">
                    <strong>State:</strong> <span><?php echo htmlspecialchars($row['state']); ?></span>
                </div>
            </div>
        </section>
    </div>
    <form action="verify_application.php" method="POST">
<input type="number" value="<?php echo"$aadhaar_number";?>" name="aadhaar_number" hidden>
<button value="submit">Back</button>

    </form>
</body>
</html>




<style>
   .header img {
                    width: 50px;
                    position: center;
                    top: 10px;
                    left: 20px;
                }
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

header {
    text-align: center;
    margin-bottom: 20px;
}

header h2 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

header p {
    font-size: 12px;
    color: #666;
}

.verification {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.verification .xml-verified {
    display: flex;
    align-items: center;
    gap: 10px;
 
}
.verification .xml-verified img {
    width: 25px; 
    height: 25px; 
}

.info-section {
    display: flex;
    gap: 20px;
}

.photo {
    flex: 1;
    text-align: center;
    border: 1px solid #ddd;
    padding: 10px;
}

.photo img {
    width: 250px;
    height: auto;
    display: block;
    margin: 0 auto;
}

.details {
    flex: 2;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.details .row {
    display: flex;
    width: 100%;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding: 5px 0;
}

.details .row strong {
    flex: 1;
    font-size: 14px;
    color: #333;
}

.details .row span {
    flex: 2;
    font-size: 14px;
    color: #555;
}

</style>