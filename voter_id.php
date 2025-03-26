<?php
require 'config.php' ;

if (!isset($_GET['voter_id'])) {
    echo "Voter ID is not provided.";
    exit;
}

$voter_id = $_GET['voter_id']; 


$sql = "SELECT v.full_name, v.gender, v.date_of_birth, v.relation_name, v.permanent_address, 
               v.registration_date, v.constituency, a.image 
        FROM voter_id v
        LEFT JOIN aadhaar_data a ON v.aadhaar_number = a.aadhaar_number
        WHERE v.EPIC = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();


$voter = $result->fetch_assoc();
$stmt->close();


$officer_name = "N/A";
$officer_signature = "default_signature.jpg"; 

if ($voter && isset($voter['constituency']) && !empty($voter['constituency'])) {
    $sql_officer = "SELECT name, signature FROM election_officers WHERE con = ?";
    $stmt_officer = $conn->prepare($sql_officer);
    $stmt_officer->bind_param("s", $voter['constituency']);
    $stmt_officer->execute();
    $result_officer = $stmt_officer->get_result();
    
    if ($result_officer->num_rows > 0) {
        $officer = $result_officer->fetch_assoc();
        $officer_name = $officer['name'] ?? "N/A";
        $officer_signature = $officer['signature'] ?? "default_signature.jpg";
    }

    $stmt_officer->close();
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter ID Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }
        body {
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    position: relative;
    overflow: hidden;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 153, 51, 0.7), rgba(255, 255, 255, 0.7), rgba(19, 136, 8, 0.7));
    filter: blur(3px);
    z-index: -1; 
}

        .card-container {
            perspective: 1000px;
            margin-bottom: 20px;
            width: 2.225in;
            height: 3.475in;
        }
        .voter-id {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s;
        }
        .front, .back {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
            padding: 15px;
            border: 3px solid #fff;
            color: black;
            font-weight: bold;
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
        }

        
        .front::before, .back::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('uploads/bg3.jpg'); 
            background-size: cover;
            background-position: center;
            filter: blur(2px);
            z-index: -1;
            border-radius: 10px;
        }

        .back {
            transform: rotateY(180deg);
        }
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .logo {
            width: 25px; 
            height: 25px;
        }
        
        .photo {
            width: 90px;
            height: 120px; 
            background-color: #ffffff;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
            align-self: center;
            overflow: hidden;
        }
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        
        .signature {
            height: 50px;
            width: 80%;
            background-color: #ffffff;
            border-radius: 5px;
            margin-top: 15px;
            align-self: center;
        }
        .signature {
    height: 50px;
    width: 80%;
    background-color: #ffffff;
    border-radius: 5px;
    margin-top: 15px;
    align-self: center;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden; 
}

.signature img {
    height: 200%; 
    width: 300%;  
}


        
        .header {
            font-weight: bold;
            text-align: center;
            width: 100%;
        }

        .heading {
            font-size: 16px; 
            margin-bottom: 3px;
        }

        .sub-heading {
            font-size: 14px; 
            margin-bottom: 3px;
        }

        .details {
            font-size: 12px; 
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 10px;
        }

        
        .details div {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .flip-button {
            margin-top: 30px;
            padding: 10px 20px;
            background: #ffffff;
            color: #003399;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .flip-button:hover {
            background: #ddd;
        }

        .flipped {
            transform: rotateY(180deg);
        }
    </style>
</head>
<body>
<div class="card-container">
    <div class="voter-id" id="voterCard">
        <div class="front">
            <div class="header-container">
                <img src="uploads/logo-left-removebg-preview.png" alt="Left Logo" class="logo"> 
                <div class="header heading1">भारत निर्वाचन आयोग</div>
                <img src="uploads/logo-right-removebg-preview.png" alt="Right Logo" class="logo">
            </div>
            <div class="header heading">ELECTION COMMISSION OF INDIA</div>
            <div class="header sub-heading">IDENTITY CARD</div>
            <div class="photo">
                <img src="<?php echo htmlspecialchars($voter['image'] ?? 'default.jpg'); ?>" alt="Voter's Photo">
            </div>
            <div class="header sub-heading"><?php echo htmlspecialchars($voter_id); ?></div>
            <div class="details">
                <div><strong>ELECTOR'S NAME:</strong> <span><?php echo htmlspecialchars($voter['full_name'] ?? 'N/A'); ?></span></div>
                <div><strong>FATHER'S NAME:</strong> <span><?php echo htmlspecialchars($voter['relation_name'] ?? 'N/A'); ?></span></div>
                <div><strong>SEX:</strong> <span><?php echo htmlspecialchars($voter['gender'] ?? 'N/A'); ?></span></div>
                <div><strong>DATE OF BIRTH:</strong> <span><?php echo htmlspecialchars($voter['date_of_birth'] ?? 'N/A'); ?></span></div>
            </div>
        </div>
        <div class="back">
            <div class="details">
                <div><strong>ADDRESS:</strong></div>
                <div><?php echo nl2br(htmlspecialchars($voter['permanent_address'] ?? 'N/A')); ?></div>
                <div><strong>DATE:</strong> <?php echo htmlspecialchars($voter['registration_date'] ?? 'N/A'); ?></div>
            </div>
            <div class="signature">
                <img src="<?php echo htmlspecialchars($officer_signature); ?>" alt="Officer Signature">
            </div>
            <div class="header">OFFICER SIGNATURE</div>
            <div>20 / 10235689</div>
            <div class="details">
                <div><strong>Election Officer:</strong> <span><?php echo htmlspecialchars($officer_name); ?></span></div>
                <div><strong>Constituency:</strong> <span><?php echo htmlspecialchars($voter['constituency'] ?? 'N/A'); ?></span></div>
            </div>
        </div>
    </div>
</div>

<button class="flip-button" onclick="flipCard()">Flip</button>
<script>
    function flipCard() {
        let card = document.getElementById("voterCard");
        card.classList.toggle("flipped");
    }
</script>

</body>
</html>
