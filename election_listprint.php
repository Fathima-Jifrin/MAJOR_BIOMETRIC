<?php 

require 'config.php';


$electionId = isset($_GET['election_id']) ? $_GET['election_id'] : '';


$sql_election = "SELECT election_name, state, constituency, election_date, election_type
                 FROM elections
                 WHERE id = ?";


$stmt_election = $conn->prepare($sql_election);
$stmt_election->bind_param("s", $electionId);


$stmt_election->execute();
$result_election = $stmt_election->get_result();

$election = $result_election->fetch_assoc();

$sql_voters = "SELECT vr.full_name, vr.phone_number, vi.relation_name, vi.gender, vi.EPIC, vi.aadhaar_number, vi.constituency, vi.date_of_birth, ad.image
               FROM voter_registration vr
               JOIN voter_id vi ON vr.aadhaar_number = vi.aadhaar_number
               LEFT JOIN aadhaar_data ad ON vi.aadhaar_number = ad.aadhaar_number
               JOIN election_voters ev ON ev.voter_id = vi.EPIC
               WHERE vr.astatus = 'approved' AND ev.election_id = ?";

$stmt_voters = $conn->prepare($sql_voters);
$stmt_voters->bind_param("s", $electionId);

$stmt_voters->execute();
$result_voters = $stmt_voters->get_result();

$voters = [];
if ($result_voters->num_rows > 0) {
    while ($row = $result_voters->fetch_assoc()) {
        $voters[] = $row;
    }
} else {
    $voters = [];
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter List 2014</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        
body {
    font-family: Arial, sans-serif;
}

.voter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); 
    gap: 8px;
    padding: 10px;
}

.voter-box {
    position: relative;
    border: 1px solid #ddd;
    padding: 8px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    font-size: 12px;
    height: 180px; 
    overflow: hidden; 
}


.serial-number-box {
    background-color: #f0f0f0;
    border-radius: 5px;
    padding: 5px;
    margin-bottom: 8px;
    text-align: center;
    font-weight: bold;
}


.voter-photo {
    width: 75px;
    height: 75px; 
    border-radius: 5px;
    object-fit: cover; 
    position: absolute;
    bottom: 8px;  
    right: 8px;  
}


.voter-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start; 
    text-align: left;
    flex-grow: 1;
    padding-bottom: 50px; 
}

.voter-info p {
    margin: 4px 0;
}

.voter-info p strong {
    color: #333;
}

.voter-info .info-row {
    display: flex;
    justify-content: space-between;
    width: 100%;
}


@media print {
    body {
        margin: 0;
        padding: 0;
    }

    .voter-grid {
        grid-template-columns: repeat(3, 1fr); 
        margin: 10px;
    }

    
    .voter-box {
        font-size: 10px;
        padding: 6px;
        height: auto;
        page-break-inside: avoid; 
    }

   
    .voter-photo {
        width: 40px;
        height: 40px;
    }
   
    .no-print {
        display: none;
    }

    .text-center.mb-8 {
        page-break-after: avoid; 
    }

    
    .text-center.mb-8 h1,
    .text-center.mb-8 p {
        margin: 0;
        padding: 0;
    }

    .voter-grid {
        page-break-before: avoid; 
    }
}

    </style>
</head>
<body class="bg-gray-50 p-6">

    <div class="text-center mb-8">
        <h1 class="text-xl font-bold"><?php echo $election['election_name']; ?></h1>
        <p class="text-sm"><?php echo $election['state']; ?></p>
        <p class="text-sm">Constituency: <?php echo $election['constituency']; ?></p>
        <p class="text-sm">Election Date: <?php echo date('d M Y', strtotime($election['election_date'])); ?></p>
        <p class="text-sm">Election Type: <?php echo $election['election_type']; ?></p>
    </div>

   
    <div class="voter-grid">
        <?php foreach ($voters as $index => $voter): 
          
            $dob = new DateTime($voter['date_of_birth']);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
        ?>
            <div class="voter-box">
             
                <div class="serial-number-box">
                    Serial No: <?php echo $index + 1; ?>
                </div>

                
                <div class="voter-info">
                    <div class="info-row">
                        <p><strong>Name:</strong> <?php echo $voter['full_name']; ?></p>
                        <p><strong>Age:</strong> <?php echo $age; ?></p>
                    </div>
                    <div class="info-row">
                      <p><strong>Father's Name:</strong> <?php echo $voter['relation_name']; ?></p>
                        <p><strong>Gender:</strong> <?php echo $voter['gender']; ?></p>
                    </div>
                    <div class="info-row">
                        <p><strong>EPIC Number:</strong> <?php echo $voter['EPIC']; ?></p>
                    </div>
                </div>

                
                <?php if (!empty($voter['image'])): ?>
                    <img src="<?php echo $voter['image']; ?>" alt="Voter Photo" class="voter-photo">
                <?php else: ?>
                    <p>No photo available</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-xs mt-4 text-right">
    <p>Note: As of <?php echo date('F j, Y'); ?></p> 
    <p>Certified: <span class="font-bold">Potential Voters</span></p>
    <p>Certified: <span class="font-bold">Duplicate Voters</span></p>
</div>


    
    <div class="text-center mt-6 no-print">
        <button onclick="window.print()" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-700">Print Voter List</button>
    </div>

</body>
</html>

<?php

$stmt_voters->close();
$stmt_election->close();
$conn->close();
?>
