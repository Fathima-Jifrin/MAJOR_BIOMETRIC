<?php

include('config.php');


if (isset($_GET['id'])) {
    $candidate_id = $_GET['id'];

    
    $query = "SELECT * FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No verified candidate found with ID: " . htmlspecialchars($candidate_id);
        exit;
    }

    $stmt->close();
} else {
    echo "Candidate ID is required.";
    exit;
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verified Candidate Details</title>
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

        .detail-item span {
            display: block;
            font-size: 1rem;
            color: #555;
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

        .actions {
    margin-top: 20px;
    text-align: center;
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


.button:nth-child(2) {
    background-color: #dc3545; 
}

.button:nth-child(3) {
    background-color: #28a745; 
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
        <h1>Verified Candidate Details</h1>
        <div class="candidate-info">
            <div class="image-signature">
                <h3>Candidate Image</h3>
                <img src="<?php echo htmlspecialchars($row['candidate_image']); ?>" alt="Candidate Image" class="candidate-image">
                <h3>Signature</h3>
                <img src="<?php echo htmlspecialchars($row['signature']); ?>" alt="Candidate Signature" class="candidate-signature">
                <h3>Symbol</h3>
                <img src="<?php echo htmlspecialchars($row['symbol']); ?>" alt="Candidate Symbol" class="candidate-symbol">
            </div>

            <div class="details">
                <div class="detail-item"><label>Election ID:</label> <span><?php echo htmlspecialchars($row['election_id']); ?></span></div>
                <div class="detail-item"><label>Name:</label> <span><?php echo htmlspecialchars($row['name']); ?></span></div>
                <div class="detail-item"><label>Party:</label> <span><?php echo htmlspecialchars($row['party']); ?></span></div>
                <div class="detail-item"><label>Party Type:</label> <span><?php echo htmlspecialchars($row['party_type']); ?></span></div>
                <div class="detail-item"><label>Aadhaar Number:</label> <span><?php echo htmlspecialchars($row['aadhaar_number']); ?></span></div>
                <div class="detail-item"><label>Assets:</label> <span><?php echo htmlspecialchars($row['assets']); ?></span></div>
                <div class="detail-item"><label>Police Cases:</label> <span><?php echo htmlspecialchars($row['police_cases']); ?></span></div>
                <div class="detail-item"><label>Arms Surrendered:</label> <span><?php echo htmlspecialchars($row['arms_surrendered']); ?></span></div>
                <div class="detail-item"><label>Declaration:</label> <span><?php echo htmlspecialchars($row['declaration']); ?></span></div>
                <div class="detail-item"><label>Address:</label> <span><?php echo htmlspecialchars($row['address']); ?></span></div>
                <div class="detail-item"><label>District:</label> <span><?php echo htmlspecialchars($row['district']); ?></span></div>
                <div class="detail-item"><label>State:</label> <span><?php echo htmlspecialchars($row['state']); ?></span></div>
                <div class="detail-item"><label>Constituency:</label> <span><?php echo htmlspecialchars($row['constituency']); ?></span></div>
                <div class="detail-item"><label>Gender:</label> <span><?php echo htmlspecialchars($row['gender']); ?></span></div>
                <div class="detail-item"><label>Date of Birth:</label> <span><?php echo htmlspecialchars($row['date_of_birth']); ?></span></div>
                <div class="detail-item"><label>Age:</label> <span><?php echo htmlspecialchars($row['age']); ?></span></div>
            </div>
        </div>

        <div class="actions">
            <a href="update_candidate.php?id=<?php echo $candidate_id; ?>" class="button">Update Candidate</a>
            <a href="delete_candidate.php?id=<?php echo $candidate_id; ?>" class="button" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete Candidate</a>
            <a href="javascript:window.print()" class="button">Print Candidate Details</a>

        </div>
        
    </script>
    </div>
</body>
</html>

