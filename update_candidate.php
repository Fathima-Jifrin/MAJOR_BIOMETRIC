<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


include('config.php');


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $candidate_id = intval($_GET['id']); 

    
    $query = "SELECT * FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("No verified candidate found with ID: " . htmlspecialchars($candidate_id));
    }
    $stmt->close();
} else {
    die("Candidate ID is required.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = $conn->real_escape_string($_POST['name']);
    $party = $conn->real_escape_string($_POST['party']);
    $party_type = $conn->real_escape_string($_POST['party_type']);
    $aadhaar_number = $conn->real_escape_string($_POST['aadhaar_number']);
    $assets = $conn->real_escape_string($_POST['assets']);
    $police_cases = $conn->real_escape_string($_POST['police_cases']);
    $arms_surrendered = $conn->real_escape_string($_POST['arms_surrendered']);
    $declaration = $conn->real_escape_string($_POST['declaration']);
    $address = $conn->real_escape_string($_POST['address']);
    $district = $conn->real_escape_string($_POST['district']);
    $state = $conn->real_escape_string($_POST['state']);
    $constituency = $conn->real_escape_string($_POST['constituency']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    $age = intval($_POST['age']); 

    
    function uploadFile($file, $defaultFileName) {
        if (isset($_FILES[$file]) && $_FILES[$file]['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES[$file]["name"]);
            if (move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
                return $target_file; 
            }
        }
        return $defaultFileName; 
    }

    $candidate_image = uploadFile('candidate_image', $row['candidate_image']);
    $signature = uploadFile('signature', $row['signature']);
    $symbol = uploadFile('symbol', $row['symbol']);

    
    $query = "UPDATE candidates SET 
        name = ?, party = ?, party_type = ?, aadhaar_number = ?, assets = ?, police_cases = ?, 
        arms_surrendered = ?, declaration = ?, address = ?, district = ?, state = ?, 
        constituency = ?, gender = ?, date_of_birth = ?, age = ?, candidate_image = ?, 
        signature = ?, symbol = ? WHERE id = ?";

    $stmt = $conn->prepare($query);

    
    $stmt->bind_param('ssssssssssssssisssi', 
        $name, $party, $party_type, $aadhaar_number, $assets, $police_cases, 
        $arms_surrendered, $declaration, $address, $district, $state, 
        $constituency, $gender, $date_of_birth, $age, $candidate_image, 
        $signature, $symbol, $candidate_id
    );


if ($stmt->execute()) {
    echo "<script>
            alert('Updated Successfully!');
            window.location.href = 'viewfinalcandidate.php?id=" . $candidate_id . "';
          </script>";
    exit;
} else {
    echo "<script>
            alert('Updation Failed! Please try again.');
            window.history.back();
          </script>";
}




    $stmt->close();
}


$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Candidate</title>
    <style>
        
        body, h1, div, span, img, label {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.5;
        }

        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        
        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        
        .candidate-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        
        .image-signature {
            flex: 1;
            min-width: 250px;
            text-align: center;
            margin-bottom: 20px;
        }

        .candidate-image,
        .candidate-signature {
            max-width: 200px;
            border: 2px solid #003366;
            border-radius: 8px;
            margin-bottom: 10px;
        }

.candidate-image,
.candidate-signature,
.candidate-symbol {
    max-width: 200px;  
    height: auto;      
    border: 2px solid #003366;
    border-radius: 8px;
    margin-bottom: 10px;
}

        

        
        .details {
            flex: 2;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-item {
            background: #f1f5fc;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .detail-item label {
            display: block;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
        }

        .detail-item input,
        .detail-item select {
            display: block;
            width: 100%;
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }

        
        button {
            display: inline-block;
            background: #007bff;
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background: #0056b3;
        }

        
        .button {
            display: inline-block;
            padding: 12px 20px;
            margin: 10px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .button:hover {
            opacity: 0.8;
        }

        
        .button:first-child {
            background-color: #007bff; 
        }

        
        @media screen and (max-width: 768px) {
            .candidate-info {
                flex-direction: column;
                align-items: center;
            }

            .details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Candidate Details</h1>
        <form action="update_candidate.php?id=<?php echo $candidate_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="candidate-info">
                <div class="image-signature">
                    <h3>Candidate Image</h3>
                    <img src="<?php echo htmlspecialchars($row['candidate_image']); ?>" alt="Candidate Image" class="candidate-image">
                    <input type="file" name="candidate_image">
                    
                    <h3>Signature</h3>
                    <img src="<?php echo htmlspecialchars($row['signature']); ?>" alt="Candidate Signature" class="candidate-signature">
                    <input type="file" name="signature">
                    
                    <h3>Symbol</h3>
                    <img src="<?php echo htmlspecialchars($row['symbol']); ?>" alt="Candidate Symbol" class="candidate-symbol">
                    <input type="file" name="symbol">
                </div>

                <div class="details">
                    <div class="detail-item">
                        <label for="name">Name:</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="party">Party:</label>
                        <input type="text" name="party" value="<?php echo htmlspecialchars($row['party']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="party_type">Party Type:</label>
                        <input type="text" name="party_type" value="<?php echo htmlspecialchars($row['party_type']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="aadhaar_number">Aadhaar Number:</label>
                        <input type="text" name="aadhaar_number" value="<?php echo htmlspecialchars($row['aadhaar_number']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="assets">Assets:</label>
                        <input type="text" name="assets" value="<?php echo htmlspecialchars($row['assets']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="police_cases">Police Cases:</label>
                        <input type="text" name="police_cases" value="<?php echo htmlspecialchars($row['police_cases']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="arms_surrendered">Arms Surrendered:</label>
                        <input type="text" name="arms_surrendered" value="<?php echo htmlspecialchars($row['arms_surrendered']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="declaration">Declaration:</label>
                        <input type="text" name="declaration" value="<?php echo htmlspecialchars($row['declaration']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="address">Address:</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="district">District:</label>
                        <input type="text" name="district" value="<?php echo htmlspecialchars($row['district']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="state">State:</label>
                        <input type="text" name="state" value="<?php echo htmlspecialchars($row['state']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="constituency">Constituency:</label>
                        <input type="text" name="constituency" value="<?php echo htmlspecialchars($row['constituency']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="gender">Gender:</label>
                        <input type="text" name="gender" value="<?php echo htmlspecialchars($row['gender']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="date_of_birth">Date of Birth:</label>
                        <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($row['date_of_birth']); ?>" required>
                    </div>
                    <div class="detail-item">
                        <label for="age">Age:</label>
                        <input type="text" name="age" value="<?php echo htmlspecialchars($row['age']); ?>" required>
                    </div>
                    
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="button">Update Candidate</button>
            </div>
        </form>
    </div>
</body>
</html>

