<?php
session_start();

require 'config.php';

if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Please log in to access your dashboard.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id']; 


$query1 = "SELECT aadhaar_number, full_name FROM voter_registration WHERE id = '$voter_id'";
$result1 = $conn->query($query1);

if ($result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
    $aadhaar_number = $row1['aadhaar_number'];
    $name = $row1['full_name'];
} else {
    die("Error: Aadhaar number not found.");
}

$query2 = "SELECT EPIC, constituency FROM voter_id WHERE aadhaar_number = '$aadhaar_number'";
$result2 = $conn->query($query2);

if ($result2->num_rows > 0) {
    $row2 = $result2->fetch_assoc();
    $epic_number = $row2['EPIC'];
    $current_constituency = $row2['constituency'];
} else {
    die("Error: Voter details not found.");
}

$constituencies = [
    "Thiruvananthapuram", "Kollam", "Pathanamthitta", "Alappuzha", "Kottayam", "Idukki",
    "Ernakulam", "Thrissur", "Palakkad", "Malappuram", "Kozhikode", "Wayanad", "Kannur", "Kasaragod"
];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $new_constituency = $_POST['new_constituency'];
    $new_address = htmlspecialchars($_POST['new_address']);
    $file_name = '';

    
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['proof']['tmp_name'];
        $file_name = basename($_FILES['proof']['name']);
        $file_path = 'uploads/edit_requests/' . $file_name;

        if (!is_dir('uploads/edit_requests')) {
            mkdir('uploads/edit_requests', 0777, true);
        }

        if (!move_uploaded_file($file_tmp, $file_path)) {
            echo "<p style='color: red;'>File upload failed.</p>";
        }
    }

    $insert_query = "INSERT INTO edit_request(epic_number, aadhaar_number, old_constituency, new_constituency, new_address, proof, request_date, status) 
                     VALUES ('$epic_number', '$aadhaar_number', '$current_constituency', '$new_constituency', '$new_address', '$file_name', NOW(), 'processing')";
    
    if ($conn->query($insert_query) === TRUE) {
        echo "<script>alert('Edit request submitted successfully.'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<p style='color: red;'>Error: Could not submit your request. Please try again later.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Voter ID Request</title>
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
            padding: 0;
            margin: 0;
        }

        
        header {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
        }

        header img {
            width: 60px;
        }

        header h1 {
            font-size: 1.5em;
            margin: 10px 0;
            color: white;
        }
       
        .container { 
            max-width: 600px; 
            margin: 30px auto; 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        }
        h1 { 
            text-align: center; 
            color: #003366; 
        }
        label { 
            font-weight: bold; 
            display: block; 
            margin: 10px 0 5px; 
        }
        select, textarea, input[type="file"] { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
        }
        button { 
            width: 100%; 
            padding: 10px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        button:hover { 
            background: #0056b3; 
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 0.9em;
        }

    </style>
</head>
<body>
    <header>
        <img src="logo.png" alt="Logo">
        <h1 class="test">Election Commission of India</h1>
    </header>
    <div class="container">
        <h1>Request Voter ID Updation</h1>
        <form method="POST" enctype="multipart/form-data">
            <p><strong>Voter Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Aadhaar Number:</strong> <?php echo htmlspecialchars($aadhaar_number); ?></p>
            <p><strong>Voter ID (EPIC):</strong> <?php echo htmlspecialchars($epic_number); ?></p>

            <label for="current_constituency">Previous Constituency:</label>
            <select name="current_constituency" id="current_constituency" required>
                <option value="<?php echo htmlspecialchars($current_constituency); ?>" selected>
                    <?php echo htmlspecialchars($current_constituency); ?>
                </option>
            </select>

            <label for="new_constituency">New Constituency:</label>
            <select name="new_constituency" id="new_constituency" required>
                <option value="">-- Select Constituency --</option>
                <?php foreach ($constituencies as $constituency): ?>
                    <option value="<?php echo $constituency; ?>"><?php echo $constituency; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="new_address">New Address:</label>
            <textarea name="new_address" id="new_address" rows="4" required></textarea>

            <label for="proof">Upload Proof (Required):</label>
            <input type="file" name="proof" id="proof" accept=".jpg, .png, .pdf" required>

            <button type="submit" name="submit_request">Submit Request</button>
        </form>
    </div>
    <footer>
        &copy; <?php echo date('Y'); ?> Election Commission of India. All rights reserved.
    </footer>
</body>
</html>
