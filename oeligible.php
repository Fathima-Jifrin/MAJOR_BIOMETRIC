<?php
session_start();

require 'config.php' ;


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
    $constituency = $row1['constituency'];
} else {
    
    die('No voter ID found for this Aadhaar number.');
}



$query3 = "SELECT * FROM elections WHERE status = 'active' AND constituency = '$constituency'";


$result3 = mysqli_query($conn, $query3);


if ($result3 && mysqli_num_rows($result3) > 0) {


$upload_dir = 'uploads/proof/'; 


$query_check = "SELECT * FROM online_voting_requests WHERE voter_id = ? AND vote_state='none'";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("s", $voter_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    
    $row_check = $result_check->fetch_assoc();
    if ($row_check['status'] === 'submitted') {
        
        echo "<script>alert('Your online voting request is already being processed by the Election Commission.'); window.location.href = 'dashboard.php';</script>";
        exit();
    } elseif($row_check['status'] === 'rejected') {
        

        echo "<script>alert('Your online voting request was rejected due to " . $row_check['rejection_reason'] . ".Please contact the election authority for further details.');
        window.location.href = 'dashboard.php';
        </script>";

        exit();
    }else{
         echo "<script>alert('Your Online voting request has been approved by election commision of india ');
        window.location.href = 'dashboard.php';
        </script>";

    }
}




if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    
    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '';
        $election_id= isset($_POST['election_id']) ? htmlspecialchars($_POST['election_id']) : '';

    
    $additional_info = isset($_POST['additional_info']) ? htmlspecialchars($_POST['additional_info']) : '';
    $eligibility_status = '';
    
    
    if (isset($_POST['residency']) && isset($_POST['citizenship'])) {
        $eligibility_status = ($_POST['residency'] === 'yes' && $_POST['citizenship'] === 'yes') ? 'yes' : 'no';
    }

    $file_name = '';
    $file_path = '';

    
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['proof']['tmp_name'];
        $file_name = basename($_FILES['proof']['name']);
        $file_path = $upload_dir . $file_name;

        
        if (move_uploaded_file($file_tmp, $file_path)) {
            $file_status = "<p></p>";
        } else {
            $file_status = "<p style='color: red;'>File upload failed.</p>";
        }
    } else {
        $file_status = "<p style='color: red;'>No file uploaded or an error occurred.</p>";
    }

    
    $insert_query = "INSERT INTO online_voting_requests (voter_id,election_id,name, aadhaar_number, reason, additional_info, proof_file_name, proof_file_path, eligibility_status) 
                     VALUES (?,?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("iisssssss", $voter_id,$election_id, $name, $aadhaar_number, $reason, $additional_info, $file_name, $file_path, $eligibility_status);
    
    if ($stmt_insert->execute()) {
        echo "<script>alert('Online voting request submitted successfully for Aadhaar: $aadhaar_number'); window.location.href = 'dashboard.php';</script>";
       
    } else {
        echo "<p style='color: red;'>Error: Could not submit your request. Please try again later.</p>";
    }
}


$query2 = "SELECT * FROM elections WHERE online_voting = 'inactive' AND constituency= '$constituency' ";


$result2 = mysqli_query($conn, $query2);
}
else{
            echo "<script>alert('No election available'); window.location.href = 'dashboard.php';</script>";

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Online Voting</title>
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
    <h1>Request Online Voting</h1>
    <?php if (!isset($_POST['eligibility_check'])): ?>
        
        <form method="POST">
            <label for="residency">Are you a resident of India?</label>
            <select name="residency" id="residency" required>
                <option value="">Select...</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label for="citizenship">Are you Indian citizen?</label>
            <select name="citizenship" id="citizenship" required>
                <option value="">Select...</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label for="reason_category">What is your reason for requesting online voting?</label>
            <select name="reason_category" id="reason_category" required>
                <option value="">Select...</option>
                <option value="elderly">I am an elderly person unable to visit polling booths.</option>
                <option value="disabled">I have a physical disability that restricts me from manual voting.</option>
                <option value="out_of_town">I will be out of my registered constituency on election day.</option>
                <option value="other">Other</option>
            </select>

            <button type="submit" name="eligibility_check">Check Eligibility</button>
        </form>
    <?php elseif ($_POST['residency'] === 'yes' && $_POST['citizenship'] === 'yes' && $_POST['reason_category'] !== ''): ?>
        
        <form method="POST" enctype="multipart/form-data">
            <p style="color: green;">You are eligible to request online voting.</p>
            <p><strong>Voter Name:</strong> <?php echo htmlspecialchars($row['full_name']); ?></p>
            <p><strong>EPIC Number:</strong> <?php echo htmlspecialchars($row1['EPIC']); ?></p>
<?php
echo '<label for="reason">Please select the election available:</label>';
echo '<select name="election_id">';

while ($row = mysqli_fetch_assoc($result2)) {
    
    echo '<option value="' . $row['id'] . '">' . $row['election_name'] . '</option>';
}


echo '</select>';
?>


            <label for="reason">Please explain why you are requesting online voting:</label>
            <textarea name="reason" id="reason" rows="5" required></textarea>

            <label for="proof">Upload proof to support your request:</label>
            <input type="file" name="proof" id="proof" accept=".jpg, .png, .pdf" required>

            <label for="additional_info">Additional Information (optional):</label>
            <textarea name="additional_info" id="additional_info" rows="3"></textarea>

            <button type="submit" name="submit_request">Submit Online Voting Request</button>
        </form>
    <?php else: ?>
        
        <p class="error">Sorry, you are not eligible for online voting based on the provided information.</p>
    <?php endif; ?>
</div>
<footer>
    &copy; <?php echo date('Y'); ?> Election Commission of India. All rights reserved.
</footer>
</body>
</html>
