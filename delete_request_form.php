<?php
session_start();

require 'config.php';



if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Please log in to access your dashboard.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id']; 


$query = "SELECT * FROM voter_registration WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $name = $row['full_name'];
    $aadhaar_number = $row['aadhaar_number'];
} else {
    
    die('No voter found with this ID.');
}


$query1 = "SELECT * FROM voter_id WHERE aadhaar_number = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("s", $aadhaar_number);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row1 = $result1->fetch_assoc();

if ($row1) {
    $EPIC = $row1['EPIC'];
} else {
    
    die('No EPIC found for this Aadhaar number.');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    
    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '';
    $file_name = '';
    $file_path = '';

   
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['proof']['tmp_name'];
        $file_name = basename($_FILES['proof']['name']);
        $file_path = 'uploads/delete_requests/' . $file_name;

        
        if (!move_uploaded_file($file_tmp, $file_path)) {
            $file_status = "<p style='color: red;'>File upload failed.</p>";
        }
    }

    
    $insert_query = "INSERT INTO delete_requests (voter_id, name, aadhaar_number, EPIC, reason, proof_file_name, proof_file_path) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("issssss", $voter_id, $name, $aadhaar_number, $EPIC, $reason, $file_name, $file_path);
    
    if ($stmt_insert->execute()) {
        echo "<script>alert('Delete request submitted successfully.'); window.location.href = 'dashboard.php';</script>";
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
    <title>Delete Voter ID Request</title>
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


        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

       
        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 30px;
        }

       
        label {
            font-size: 1.1em;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }

        select, textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        select:focus, textarea:focus, input[type="file"]:focus {
            border-color: #0056b3;
            outline: none;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.2em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:active {
            background-color: #004080;
        }

        .error {
            color: red;
            font-size: 1.1em;
            margin-top: 20px;
        }

        
        p.green {
            color: green;
            font-size: 1.1em;
        }

       
        .test {
            color: white;
        }

        span {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1.1em;
        }

      
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.2em;
            }

            .container {
                padding: 15px;
            }

            button {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
<header>
    <img src="logo.png" alt="Logo">
    <h1 class="test">Election Commission of India</h1>
</header>
<div class="container">
    <h1>Request Voter ID Deletion</h1>
    <form method="POST" enctype="multipart/form-data">
        <p><strong>Voter Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>EPIC Number:</strong> <?php echo htmlspecialchars($EPIC); ?></p>
        <p><strong>Aadhaar Number:</strong> <?php echo htmlspecialchars($aadhaar_number); ?></p>

      
        <label for="reason">Reason for Deletion:</label>
        <textarea name="reason" id="reason" rows="5" required></textarea>

       
        <label for="proof">Upload Proof (Optional):</label>
        <input type="file" name="proof" id="proof" accept=".jpg, .png, .pdf">

        <button type="submit" name="submit_request">Submit Delete Request</button>
    </form>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Election Commission of India. All rights reserved.
</footer>
</body>
</html>
