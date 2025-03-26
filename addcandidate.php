<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


require 'config.php';

$election_id = $_GET['id'];

$sql = "SELECT  election_name FROM elections WHERE status='active' AND  id='$election_id'";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
$electname=$row['election_name'];
   
    }
} else {
    echo "election not available";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $party = $conn->real_escape_string($_POST['party'] ?? '');
    $party_type = $conn->real_escape_string($_POST['party_type'] ?? '');
    $aadhaar_number = $conn->real_escape_string($_POST['aadhaar_number'] ?? '');
    $assets = $conn->real_escape_string($_POST['assets'] ?? '');
    $police_cases = $conn->real_escape_string($_POST['police_cases'] ?? '');
    $arms_surrendered = $conn->real_escape_string($_POST['arms_surrendered'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $district = $conn->real_escape_string($_POST['district'] ?? '');
    $state = $conn->real_escape_string($_POST['state'] ?? '');
    $constituency = $conn->real_escape_string($_POST['constituency'] ?? '');
    $gender = $conn->real_escape_string($_POST['gender'] ?? '');
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth'] ?? '');
    $election_id_number = $conn->real_escape_string($_POST['election_id_number'] ?? '');

    
    $age = !empty($date_of_birth) ? date_diff(date_create($date_of_birth), date_create('today'))->y : 0;

    
    $uploads_dir = 'uploads/';
    $symbol_path = $uploads_dir . 'symbols/' . basename($_FILES['symbol']['name']);
    $declaration_path = $uploads_dir . 'declarations/' . basename($_FILES['declaration']['name']);
    $image_path = $uploads_dir . 'images/' . basename($_FILES['candidate_image']['name']);
    $signature_path = $uploads_dir . 'signatures/' . basename($_FILES['signature']['name']);

    
    if (
        move_uploaded_file($_FILES['symbol']['tmp_name'], $symbol_path) &&
        move_uploaded_file($_FILES['declaration']['tmp_name'], $declaration_path) &&
        move_uploaded_file($_FILES['candidate_image']['tmp_name'], $image_path) &&
        move_uploaded_file($_FILES['signature']['tmp_name'], $signature_path)
    ) {
        
        $insert_query = "INSERT INTO candidates 
            (name,election_id, party, party_type, aadhaar_number, assets, police_cases, arms_surrendered, symbol, declaration, candidate_image, signature, verified, address, district, state, constituency, gender, date_of_birth, age, election_id_number) 
            VALUES ('$name','$election_id', '$party', '$party_type', '$aadhaar_number', '$assets', '$police_cases', '$arms_surrendered', '$symbol_path', '$declaration_path', '$image_path', '$signature_path', 'Pending', '$address', '$district', '$state', '$constituency', '$gender', '$date_of_birth', $age, '$election_id_number')";

        if ($conn->query($insert_query)) {
            
            echo "<script>
        alert('Candidate added successfully.');
        window.location.href = 'selectelection.php';
      </script>";

        } else {
            $error_message = "Error adding candidate: " . $conn->error;
        }
    } else {
        $error_message = "Error uploading files.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
   
    <header>
        <img src="logo.png" alt="Election Commission Logo" width="100" height="100" style="display: block; margin: 0 auto;">
        <h1 style="text-align: center; color: white;">Election Commission of India</h1>
    </header>

    <div class="container">
        <h2>Candidate Application for <?php echo $electname ;?></h2>

        <?php if (isset($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        
        <form id="candidate-form" method="post" enctype="multipart/form-data">
           
            <div class="form-section" id="section1">
                <h3>Section 1: Basic Details</h3>
                <input type="text" name="name" placeholder="Candidate Name" required>
                <input type="text" name="party" placeholder="Party Name (or 'Independent')" required>
                <select name="party_type" required>
                    <option value="Independent">Independent</option>
                    <option value="Party">Party</option>
                </select>
                <input type="text" name="aadhaar_number" placeholder="Aadhaar Number" required>
                <textarea name="assets" placeholder="Assets Details" required></textarea>
                <textarea name="police_cases" placeholder="Police Cases"></textarea>
                <select name="arms_surrendered" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
                <button type="button" class="next">Next</button>
            </div>

            
            <div class="form-section" id="section2" style="display: none;">
                <h3>Section 2: Upload Documents</h3>
                <label for="symbol">Party Symbol (Image)</label>
                <input type="file" name="symbol" accept="image/*" required>
                <label for="declaration">Declaration (PDF/DOCX)</label>
                <input type="file" name="declaration" accept=".pdf,.doc,.docx" required>
                <label for="candidate_image">Candidate Image (JPG/PNG)</label>
                <input type="file" name="candidate_image" accept="image/*" required>
                <label for="signature">Signature (Image)</label>
                <input type="file" name="signature" accept="image/*" required>
                <textarea name="address" placeholder="Address" required></textarea>
                <input type="text" name="district" placeholder="District" required>
                <input type="text" name="state" placeholder="State" required>
                <input type="text" name="constituency" placeholder="Constituency" required>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <input type="date" name="date_of_birth" required>
                <input type="text" name="election_id_number" placeholder="Election ID Number" required>
                <button type="button" class="previous">Previous</button>
                <button type="submit">Add Candidate</button>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Election Commission of India Center. All Rights Reserved.</p>
    </footer>

    <script>
        
        const nextButton = document.querySelector('.next');
        const previousButton = document.querySelector('.previous');
        const section1 = document.getElementById('section1');
        const section2 = document.getElementById('section2');

        nextButton.addEventListener('click', () => {
            section1.style.display = 'none';
            section2.style.display = 'block';
        });

        previousButton.addEventListener('click', () => {
            section2.style.display = 'none';
            section1.style.display = 'block';
        });
    </script>
</body>
</html>


<style>
header{
    background-color:#003366;
    color:white;
    height:12%;
}
.c a{
 
    margin:50px;
    position:relative;
    text-decoration:none;
    font-size:large;
    font-style:bold;
}

button#previous2 {
    background-color: #dc3545;
    margin-top: 10px;
}

button#previous2:hover {
    background-color: #c82333;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    color: #333;
}


.container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}


h1 {
    text-align: center;
    color: #003366;
    font-size: 36px;
    margin-bottom: 30px;
}

h2 {
    text-align: center;
    color: #003366;
    font-size: 28px;
    margin-bottom: 20px;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 2px solid #003366;
    border-radius: 5px;
    font-size: 16px;
}

input[type="file"] {
    padding: 5px;
}

button {
    background-color: #003366;
    color: white;
    padding: 14px 20px;
    font-size: 18px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    margin-top: 20px;
}

button:hover {
    background-color: #00509e;
}


p {
    text-align: center;
    font-size: 18px;
    margin-top: 20px;
}

p[style="color: green;"] {
    color: #28a745;
}

p[style="color: red;"] {
    color: #dc3545;
}


input[type="file"] {
    padding: 10px;
}

form {
    width: 80%;
    margin: 0 auto;
}

form input[type="text"],
form input[type="date"],
form select,
form textarea,
form input[type="file"] {
    margin: 15px 0;
}


select {
    background-color: #f8f9fa;
}

button#next1 {
    background-color: #28a745;
}

button#next1:hover {
    background-color: #218838;
}


.form-section {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 20px;
}

form#form1 .form-section {
    background-color: #e3f2fd;
}

form#form2 .form-section {
    background-color: #e8f5e9;
}

footer {
    text-align: center;
    background-color: #003366;
    color: #fff;
    padding: 10px;
    position: fixed;
    bottom: 0;
    width: 100%;
    font-size: 14px;
}

footer p {
    margin: 0;
}

footer a {
    color: #ffc107;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}

</style>