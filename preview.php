<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $full_name = $_POST['full_name'];
    $surname = $_POST['surname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $place_of_birth = $_POST['place_of_birth'];
    $current_address = $_POST['current_address'];
    $permanent_address = $_POST['permanent_address'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $pincode = $_POST['pincode'];
    $constituency = $_POST['constituency'];
    $relation_type = $_POST['relation_type'];
    $relation_name = $_POST['relation_name'];
    $declaration_date = $_POST['declaration_date'];
    $applicant_signature = $_POST['applicant_signature'];
    $aadhaar_number = $_POST['adhaar'];
    $voter_id = $_POST['voter_id'];


    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Voter Registration Application Preview</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f4f4f4;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
                text-align: center;
                color: #0056b3;
            }
            h3 {
                color: #333;
                margin-top: 20px;
            }
            p {
                margin: 10px 0;
            }
            .section {
                margin-bottom: 20px;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background-color: #f9f9f9;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                width: 100px;
            }
            .column {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }
            .column div {
                width: 48%; 
            }
            .final-submit {
                text-align: center;
                margin-top: 20px;
            }
            input[type="submit"] {
                padding: 10px 20px;
                background-color: #0056b3;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            input[type="submit"]:hover {
                background-color: #004494;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <img src="logo.png" alt="Election Commission Logo"> 
                <h2>Election Commission of India</h2>
            </div>

            <h2>Voter Registration Application Preview</h2>

            <div class="section">
                <h3>Applicant Information</h3>
                <div class="column">
                    <div>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                        <p><strong>Surname:</strong> <?php echo htmlspecialchars($surname); ?></p>
                    </div>
                    <div>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($dob); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
                    </div>
                </div>
                <p><strong>Place of Birth:</strong> <?php echo htmlspecialchars($place_of_birth); ?></p>
            </div>

            <div class="section">
                <h3>Address Information</h3>
                <div class="column">
                    <div>
                        <p><strong>Current Address:</strong> <?php echo htmlspecialchars($current_address); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($state); ?></p>
                        <p><strong>District:</strong> <?php echo htmlspecialchars($district); ?></p>
                    </div>
                    <div>
                        <p><strong>Permanent Address:</strong> <?php echo htmlspecialchars($permanent_address); ?></p>
                        <p><strong>Pincode:</strong> <?php echo htmlspecialchars($pincode); ?></p>
                        <p><strong>Constituency:</strong> <?php echo htmlspecialchars($constituency); ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>Parent's Information</h3>
                <div class="column">
                    <div>
                        <p><strong>Relation Type:</strong> <?php echo htmlspecialchars($relation_type); ?></p>
                    </div>
                    <div>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($relation_name); ?></p>
                    </div>
                      <div>
                        <p><strong>Voter Id:</strong> <?php echo htmlspecialchars($voter_id); ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>Declaration</h3>
                <div class="column">
                    <div>
                        <p><strong>Declaration Date:</strong> <?php echo htmlspecialchars($declaration_date); ?></p>
                    </div>
                    <div>
                        <p><strong>Place:</strong> <?php echo htmlspecialchars($applicant_signature); ?></p>
                    </div>
                </div>
            </div>

            <div class="final-submit">
                <form action="submit_form_a.php" method="post">
                    <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>">
                    <input type="hidden" name="surname" value="<?php echo htmlspecialchars($surname); ?>">
                    <input type="hidden" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
                    <input type="hidden" name="gender" value="<?php echo htmlspecialchars($gender); ?>">
                    <input type="hidden" name="place_of_birth" value="<?php echo htmlspecialchars($place_of_birth); ?>">
                    <input type="hidden" name="current_address" value="<?php echo htmlspecialchars($current_address); ?>">
                    <input type="hidden" name="permanent_address" value="<?php echo htmlspecialchars($permanent_address); ?>">
                    <input type="hidden" name="state" value="<?php echo htmlspecialchars($state); ?>">
                    <input type="hidden" name="district" value="<?php echo htmlspecialchars($district); ?>">
                    <input type="hidden" name="pincode" value="<?php echo htmlspecialchars($pincode); ?>">
                    <input type="hidden" name="constituency" value="<?php echo htmlspecialchars($constituency); ?>">
                    <input type="hidden" name="relation_type" value="<?php echo htmlspecialchars($relation_type); ?>">
                    <input type="hidden" name="relation_name" value="<?php echo htmlspecialchars($relation_name); ?>">
                     <input type="hidden" name="voter_id" value="<?php echo htmlspecialchars($voter_id); ?>">
                    <input type="hidden" name="declaration_date" value="<?php echo htmlspecialchars($declaration_date); ?>">
                    <input type="hidden" name="applicant_signature" value="<?php echo htmlspecialchars($applicant_signature); ?>">
                    <input type="hidden" name="aadhaar" value="<?php echo htmlspecialchars($aadhaar_number); ?>">
                    <input type="submit" value="Final Submit">
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "<script>alert('No data received.'); window.location.href='registration.php';</script>";
}
?>
