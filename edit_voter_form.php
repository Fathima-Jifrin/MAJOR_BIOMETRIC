<?php
session_start();

require 'config.php';

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access this page.'); window.location.href='adminlog.html';</script>";
    exit();
}

if (!isset($_GET['aadhaar_number']) || empty($_GET['aadhaar_number'])) {
    die("Aadhaar Number not provided.");
}

$aadhaar_number = $_GET['aadhaar_number'];


$sql = "SELECT v.*, a.image FROM voter_id v 
        LEFT JOIN aadhaar_data a ON v.aadhaar_number = a.aadhaar_number 
        WHERE v.aadhaar_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $aadhaar_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No voter found with this Aadhaar Number.");
}

$voter = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Voter</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: darkblue;
            color: white;
            padding: 15px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }
        .back-button-container {
            margin-top: 20px;
            margin-left: -93%;
            text-align: left;
        }
        .back-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #004494;
        }
        .container {
            max-width: 800px;
            background: white;
            margin: 30px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
    
        .form-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .form-group label {
            flex: 1;
            font-weight: bold;
            margin-right: 15px;
        }
        .form-group input {
            flex: 2;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .readonly {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        
        
        .image-container {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            margin-right: 15px;
        }
        .image-container img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            border: 2px solid #ccc;
        }

        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background-color: #218838;
        }

        
        .back-button {
            display: block;
            text-align: center;
            margin: 15px auto;
            padding: 10px 15px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            width: fit-content;
        }
        .back-button:hover {
            background-color: #004494;
        }
    </style>
</head>
<body>

<header>
        <div class="logo">
            <img src="logo.png" alt="ECI Logo" width="100" height="100">
        </div>
        <h1>Election Commission of India</h1>
    </header>
    <div class="back-button-container">
        <a href="edit_voter.php" class="back-button">← Back</a>
    </div>

<div class="container">
    <form action="edit_voter_submit.php" method="post">
        
        <div class="image-container">
            <label>Voter Image:</label><br><br>
            <img src="<?php echo($voter['image']); ?>" alt="Voter Image">
        </div>

        <div class="form-group">
            <label>Application ID:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['application_id']); ?>" class="readonly" readonly>
        </div>

        
        <div class="form-group">
            <label>Aadhaar Number:</label>
            <input type="text" name="aadhaar_number" value="<?php echo htmlspecialchars($voter['aadhaar_number']); ?>" class="readonly" readonly>
        </div>

      
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['full_name']); ?>" class="readonly" readonly>
        </div>

      
        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['phone_number']); ?>" class="readonly" readonly>
        </div>

       
        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['date_of_birth']); ?>" class="readonly" readonly>
        </div>

       
        <div class="form-group">
            <label>Email:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['email']); ?>" class="readonly" readonly>
        </div>

        <div class="form-group">
            <label>Registration Date:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['registration_date']); ?>" class="readonly" readonly>
        </div>

        
        <div class="form-group">
            <label>Gender:</label>
            <input type="text" value="<?php echo htmlspecialchars($voter['gender']); ?>" class="readonly" readonly>
        </div>

        
        <div class="form-group">
            <label>Current Address:</label>
            <input type="text" name="current_address" value="<?php echo htmlspecialchars($voter['current_address']); ?>" required>
        </div>

       
        <div class="form-group">
            <label>Permanent Address:</label>
            <input type="text" name="permanent_address" value="<?php echo htmlspecialchars($voter['permanent_address']); ?>" required>
        </div>



        
        <div class="form-group">
            <label>Constituency:</label>
            <input type="text" name="constituency" value="<?php echo htmlspecialchars($voter['constituency']); ?>" required>
        </div>

        <button type="submit" class="submit-btn">Update</button>
    </form>
</div>



</body>
</html>


<?php
$conn->close();
?>
