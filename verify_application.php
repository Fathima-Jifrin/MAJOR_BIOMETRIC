<?php
session_start();

require 'config.php' ;

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access your dashboard.'); window.location.href='adminlog.html';</script>";
    exit();
}





if (isset($_POST['aadhaar_number'])) {
    $aadhaar_number = $_POST['aadhaar_number'];


    
    $_SESSION['aadhaar_number'] = $aadhaar_number;
    $reject_reason_sql = "SELECT rejectreason FROM voter_registration WHERE aadhaar_number = '$aadhaar_number'";
$reject_reason_result = $conn->query($reject_reason_sql);

$reject_reason = '';
if ($reject_reason_result && $reject_reason_result->num_rows > 0) {
    $reject_reason_row = $reject_reason_result->fetch_assoc();
    $reject_reason = $reject_reason_row['rejectreason'];
}

    
    $sql = "SELECT * FROM voter_id WHERE aadhaar_number = '$aadhaar_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();



        $aadhaar_sql = "SELECT * FROM aadhaar_data WHERE aadhaar_number = '$aadhaar_number'";
        $aadhaar_result = $conn->query($aadhaar_sql);

        
        $verification_status = '';
        $verification_icon = ''; 

        if ($aadhaar_result->num_rows > 0) {
            $aadhaar_row = $aadhaar_result->fetch_assoc();

            
            $full_name = $aadhaar_row['full_name'];
            $dob = $aadhaar_row['date_of_birth'];
        } else {
            $full_name = '';
            $dob = '';
        }
     
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Application Verification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    background-color: #fff;
                    padding: 20px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    margin-bottom: 20px;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                }
                .section {
                    flex: 1 1 45%;
                    background-color: #f9f9f9;
                    padding: 15px;
                    border-radius: 5px;
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                }
                h2 {
                    text-align: center;
                }
                .detail {
                    margin-bottom: 10px;
                    display: flex;
                    justify-content: space-between;
                }
                .label {
                    font-weight: bold;
                }
                .buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                    margin-top: 20px;
                }
                .button {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    color: #fff;
                    background-color: #007bff;
                }
                .button:hover {
                    background-color: #0056b3;
                }
                .approve {
                    background-color: #28a745;
                }
                .approve:hover {
                    background-color: #218838;
                }
                .reject {
                    background-color: #dc3545;
                }
                .reject:hover {
                    background-color: #c82333;
                }
                .revert {
                    background-color: #ffc107;
                    color: #212529;
                }
                .revert:hover {
                    background-color: #e0a800;
                }
                .header {
                    background-color: #001f5b;
                    color: #ffffff;
                    padding: 15px 20px;
                    text-align: center;
                    position: relative;
                }
                .header img {
                    width: 75px;
                    position: center;
                    top: 10px;
                    left: 20px;
                }
                .footer {
                    background-color: #001f5b;
                    color: #ffffff;
                    padding: 10px 0;
                    text-align: center;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
         <div class="header">
                <img src="logo.png" alt="Election Commission Logo">
                <h1>Election Commission</h1>
            </div>
            <div class="container">
                <div class="section">
                    <h2>Personal Details</h2>
                    <div class="detail"><div class="label">Application ID:</div><div><?php echo htmlspecialchars($row['application_id']); ?></div></div>
                    <div class="detail"><div class="label">Full Name:</div><div><?php echo htmlspecialchars($row['full_name']); ?></div></div>
        <div class="detail"><div class="label">Aadhaar Number:</div><div><?php echo htmlspecialchars($row['aadhaar_number']); ?> <span id="aadhaar-verification-icon"></span></div></div>
                    <button class="button" onclick="verifyAadhaar('<?php echo $full_name; ?>', '<?php echo $dob; ?>')">Verify Aadhaar</button>
                     <button class="button" onclick="viewAadhaar()">View Aadhaar</button>
                </div>
                
                <div class="section">
                    <h2>Contact Information</h2>
                    <div class="detail"><div class="label">Phone Number:</div><div><?php echo htmlspecialchars($row['phone_number']); ?></div></div>
                    <div class="detail"><div class="label">Email:</div><div><?php echo htmlspecialchars($row['email']); ?></div></div>
                    <div class="detail"><div class="label">Date of Birth:</div><div><?php echo htmlspecialchars($row['date_of_birth']); ?></div></div>
                </div>
                
                <div class="section">
                    <h2>Address Details</h2>
                    <div class="detail"><div class="label">Current Address:</div><div><?php echo htmlspecialchars($row['current_address']); ?></div></div>
                    <div class="detail"><div class="label">Permanent Address:</div><div><?php echo htmlspecialchars($row['permanent_address']); ?></div></div>
                    <div class="detail"><div class="label">Pincode:</div><div><?php echo htmlspecialchars($row['pincode']); ?></div></div>
                </div>
                
                <div class="section">
                    <h2>Additional Details</h2>
                    <div class="detail"><div class="label">State:</div><div><?php echo htmlspecialchars($row['state']); ?></div></div>
                    <div class="detail"><div class="label">District:</div><div><?php echo htmlspecialchars($row['district']); ?></div></div>
                    <div class="detail"><div class="label">Constituency:</div><div><?php echo htmlspecialchars($row['constituency']); ?></div></div>
                </div>
                
               

<div class="section">
    <h2>Family Details</h2>
    <div class="detail">
        <div class="label">Relation Type:</div>
        <div><?php echo htmlspecialchars($row['relation_type']); ?></div>
    </div>
    <div class="detail">
        <div class="label">Relation Name:</div>
        <div><?php echo htmlspecialchars($row['relation_name']); ?></div>
    </div>
    <div class="detail">
        <div class="label">Father's Place:</div>
        <div><?php echo htmlspecialchars($row['father_place']); ?></div>
    </div>
    <div class="detail">
        <div class="label">Voter ID:</div>
        <div>
            <?php echo htmlspecialchars($row['rvoter_id']); ?>
            
           <button class="verify-button" onclick="verifyVoter('<?php echo htmlspecialchars($row['rvoter_id']); ?>', '<?php echo htmlspecialchars($row['aadhaar_number']); ?>')">Verify</button>

        </div>
    </div>
</div>
<div class="section">
    <h2>Rejection Reason</h2>
    <?php if (!empty($reject_reason)): ?>
        <div class="detail">
            <div class="label">Reason for Rejection:</div>
            <div style="color: red;"><?php echo htmlspecialchars($reject_reason); ?></div>
        </div>
    <?php else: ?>
        <div class="detail">
            <div class="label">Rejection Status:</div>
            <div>No rejection reason available.</div>
        </div>
    <?php endif; ?>
</div>
<div id="rejectModal" style="display:none;">
    
    <div class="modal-content">
        
        <h3>Election Commission of India Rejection Form</h3>

        
        <button class="close-btn" onclick="closeModal()">Cancel</button>

        
        <form id="rejectForm" method="POST" action="reject_application.php">
            <label for="rejectReasonText">Reject Reason:</label><br>
<textarea id="rejectReasonText" name="rejectReason" rows="4" cols="50"><?php echo $reject_reason; ?></textarea><br><br>

            <label for="errorType">Error Type:</label><br>
            <select id="errorType" name="errorType">
                <option value="missing_documents">Missing Documents</option>
                <option value="invalid_data">Invalid Data</option>
                <option value="incomplete_form">Incomplete Form</option>
                
            </select><br><br>

            <input type="submit" value="Submit Rejection">
        </form>
    </div>
</div>

            </div>
            
            <div class="buttons">
    <button class="button approve" onclick="approveApplication()">Approve Application</button>
                <button class="button reject" onclick="rejectApplication()">Reject Application</button>
                <button class="button revert" onclick="alert('Application Reverted')">Revert Application</button>
            </div>
  <div class="footer">
                &copy; 2024 Election Commission. All Rights Reserved.
            </div>
          <script>
                function verifyAadhaar(aadhaarFullName, aadhaarDob) {
                    
                    var formFullName = "<?php echo $row['full_name']; ?>";
                    var formDob = "<?php echo $row['date_of_birth']; ?>";
                    
                    var verificationStatus = '';
                    var verificationIcon = '';

                    
                    if (aadhaarFullName === formFullName && aadhaarDob === formDob) {
                        verificationStatus = "Aadhaar verification successful.";
                        verificationIcon = "<span style='color:green;'>✔️</span>"; 
                    } else {
                        verificationStatus = "Aadhaar verification failed: Name or date of birth does not match.";
                        verificationIcon = "<span style='color:red;'>❌</span>"; 
                    }

                    
                    document.getElementById('aadhaar-verification-icon').innerHTML = verificationIcon;
                    alert(verificationStatus);
                }
                   const aadhaarNumber = "<?php echo htmlspecialchars($aadhaar_number); ?>";
            function viewAadhaar() {
    const aadhaarInput =  "<?php echo htmlspecialchars($aadhaar_number); ?>";
    const encodedAadhaar = btoa(aadhaarInput); 

    
    window.location.href = `adhaar.php?aadhaar_number=${encodeURIComponent(encodedAadhaar)}`;
}
  function verifyVoter(voterId) {
        
        window.location.href = `verify_voter.php?voter_id=${encodeURIComponent(voterId)}`;
    }
        
            </script>
        </body>
        </html>
        <?php
    } else {
        echo "<p>No details found for the given Aadhaar number.</p>";
    }
} elseif (isset($_GET['aadhaar_number'])) {
    $aadhaar_number = $_GET['aadhaar_number'];
      
    $_SESSION['aadhaar_number'] = $aadhaar_number;
        $reject_reason_sql = "SELECT rejectreason FROM voter_registration WHERE aadhaar_number = '$aadhaar_number'";
$reject_reason_result = $conn->query($reject_reason_sql);

$reject_reason = '';
if ($reject_reason_result && $reject_reason_result->num_rows > 0) {
    $reject_reason_row = $reject_reason_result->fetch_assoc();
    $reject_reason = $reject_reason_row['rejectreason'];
}

    
    $sql = "SELECT * FROM voter_id WHERE aadhaar_number = '$aadhaar_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();



        $aadhaar_sql = "SELECT * FROM aadhaar_data WHERE aadhaar_number = '$aadhaar_number'";
        $aadhaar_result = $conn->query($aadhaar_sql);

        
        $verification_status = '';
        $verification_icon = ''; 

        if ($aadhaar_result->num_rows > 0) {
            $aadhaar_row = $aadhaar_result->fetch_assoc();

            
            $full_name = $aadhaar_row['full_name'];
            $dob = $aadhaar_row['date_of_birth'];
        } else {
            $full_name = '';
            $dob = '';
        }
     
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Application Verification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    background-color: #fff;
                    padding: 20px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    margin-bottom: 20px;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                }
                .section {
                    flex: 1 1 45%;
                    background-color: #f9f9f9;
                    padding: 15px;
                    border-radius: 5px;
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                }
                h2 {
                    text-align: center;
                }
                .detail {
                    margin-bottom: 10px;
                    display: flex;
                    justify-content: space-between;
                }
                .label {
                    font-weight: bold;
                }
                .buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                    margin-top: 20px;
                }
                .button {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    color: #fff;
                    background-color: #007bff;
                }
                .button:hover {
                    background-color: #0056b3;
                }
                .approve {
                    background-color: #28a745;
                }
                .approve:hover {
                    background-color: #218838;
                }
                .reject {
                    background-color: #dc3545;
                }
                .reject:hover {
                    background-color: #c82333;
                }
                .revert {
                    background-color: #ffc107;
                    color: #212529;
                }
                .revert:hover {
                    background-color: #e0a800;
                }
                .header {
                    background-color: #001f5b;
                    color: #ffffff;
                    padding: 15px 20px;
                    text-align: center;
                    position: relative;
                }
                .header img {
                    width: 75px;
                    position: center;
                    top: 10px;
                    left: 20px;
                }
                .footer {
                    background-color: #001f5b;
                    color: #ffffff;
                    padding: 10px 0;
                    text-align: center;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
         <div class="header">
                <img src="logo.png" alt="Election Commission Logo">
                <h1>Election Commission</h1>
            </div>
            <div class="container">
                <div class="section">
                    <h2>Personal Details</h2>
                    <div class="detail"><div class="label">Application ID:</div><div><?php echo htmlspecialchars($row['application_id']); ?></div></div>
                    <div class="detail"><div class="label">Full Name:</div><div><?php echo htmlspecialchars($row['full_name']); ?></div></div>
        <div class="detail"><div class="label">Aadhaar Number:</div><div><?php echo htmlspecialchars($row['aadhaar_number']); ?> <span id="aadhaar-verification-icon"></span></div></div>
                    <button class="button" onclick="verifyAadhaar('<?php echo $full_name; ?>', '<?php echo $dob; ?>')">Verify Aadhaar</button>
                     <button class="button" onclick="viewAadhaar()">View Aadhaar</button>
                </div>
                
                <div class="section">
                    <h2>Contact Information</h2>
                    <div class="detail"><div class="label">Phone Number:</div><div><?php echo htmlspecialchars($row['phone_number']); ?></div></div>
                    <div class="detail"><div class="label">Email:</div><div><?php echo htmlspecialchars($row['email']); ?></div></div>
                    <div class="detail"><div class="label">Date of Birth:</div><div><?php echo htmlspecialchars($row['date_of_birth']); ?></div></div>
                </div>
                
                <div class="section">
                    <h2>Address Details</h2>
                    <div class="detail"><div class="label">Current Address:</div><div><?php echo htmlspecialchars($row['current_address']); ?></div></div>
                    <div class="detail"><div class="label">Permanent Address:</div><div><?php echo htmlspecialchars($row['permanent_address']); ?></div></div>
                    <div class="detail"><div class="label">Pincode:</div><div><?php echo htmlspecialchars($row['pincode']); ?></div></div>
                </div>
                
                <div class="section">
                    <h2>Additional Details</h2>
                    <div class="detail"><div class="label">State:</div><div><?php echo htmlspecialchars($row['state']); ?></div></div>
                    <div class="detail"><div class="label">District:</div><div><?php echo htmlspecialchars($row['district']); ?></div></div>
                    <div class="detail"><div class="label">Constituency:</div><div><?php echo htmlspecialchars($row['constituency']); ?></div></div>
                </div>
                
               

<div class="section">
    <h2>Family Details</h2>
    <div class="detail">
        <div class="label">Relation Type:</div>
        <div><?php echo htmlspecialchars($row['relation_type']); ?></div>
    </div>
    <div class="detail">
        <div class="label">Relation Name:</div>
        <div><?php echo htmlspecialchars($row['relation_name']); ?></div>
    </div>
    <div class="detail">
        <div class="label">Father's Place:</div>
        <div><?php echo htmlspecialchars($row['father_place']); ?></div>
    </div>
    <div class="detail">
        <div class="label">Voter ID:</div>
        <div>
            <?php echo htmlspecialchars($row['rvoter_id']); ?>
            
           <button class="verify-button" onclick="verifyVoter('<?php echo htmlspecialchars($row['rvoter_id']); ?>', '<?php echo htmlspecialchars($row['aadhaar_number']); ?>')">Verify</button>

        </div>
    </div>
</div>
<div class="section">
    <h2>Rejection Reason</h2>
    <?php if (!empty($reject_reason)): ?>
        <div class="detail">
            <div class="label">Reason for Rejection:</div>
            <div style="color: red;"><?php echo htmlspecialchars($reject_reason); ?></div>
        </div>
    <?php else: ?>
        <div class="detail">
            <div class="label">Rejection Status:</div>
            <div>No rejection reason available.</div>
        </div>
    <?php endif; ?>
</div>
<div id="rejectModal" style="display:none;">
    
    <div class="modal-content">
        
        <h3>Election Commission of India Rejection Form</h3>

        
        <button class="close-btn" onclick="closeModal()">Cancel</button>

        
        <form id="rejectForm" method="POST" action="reject_application.php">
            <label for="rejectReasonText">Reject Reason:</label><br>
<textarea id="rejectReasonText" name="rejectReason" rows="4" cols="50"><?php echo $reject_reason; ?></textarea><br><br>

            <label for="errorType">Error Type:</label><br>
            <select id="errorType" name="errorType">
                <option value="missing_documents">Missing Documents</option>
                <option value="invalid_data">Invalid Data</option>
                <option value="incomplete_form">Incomplete Form</option>
                
            </select><br><br>

            <input type="submit" value="Submit Rejection">
        </form>
    </div>
</div>

            </div>
            
            <div class="buttons">
    <button class="button approve" onclick="approveApplication()">Approve Application</button>
                <button class="button reject" onclick="rejectApplication()">Reject Application</button>
                <button class="button revert" onclick="alert('Application Reverted')">Revert Application</button>
            </div>
  <div class="footer">
                &copy; 2024 Election Commission. All Rights Reserved.
            </div>
          <script>
                function verifyAadhaar(aadhaarFullName, aadhaarDob) {
                    
                    var formFullName = "<?php echo $row['full_name']; ?>";
                    var formDob = "<?php echo $row['date_of_birth']; ?>";
                    
                    var verificationStatus = '';
                    var verificationIcon = '';

                    
                    if (aadhaarFullName === formFullName && aadhaarDob === formDob) {
                        verificationStatus = "Aadhaar verification successful.";
                        verificationIcon = "<span style='color:green;'>✔️</span>"; 
                    } else {
                        verificationStatus = "Aadhaar verification failed: Name or date of birth does not match.";
                        verificationIcon = "<span style='color:red;'>❌</span>"; 
                    }

                    
                    document.getElementById('aadhaar-verification-icon').innerHTML = verificationIcon;
                    alert(verificationStatus);
                }
                   const aadhaarNumber = "<?php echo htmlspecialchars($aadhaar_number); ?>";
            function viewAadhaar() {
    const aadhaarInput =  "<?php echo htmlspecialchars($aadhaar_number); ?>";
    const encodedAadhaar = btoa(aadhaarInput); 

    
    window.location.href = `adhaar.php?aadhaar_number=${encodeURIComponent(encodedAadhaar)}`;
}
  function verifyVoter(voterId) {
        
        window.location.href = `verify_voter.php?voter_id=${encodeURIComponent(voterId)}`;
    }
        
            </script>
        </body>
        </html>
        <?php
    } else {
        echo "<p>No details found for the given Aadhaar number.</p>";
    }


}

 else {
    echo "<p>Aadhaar number not provided.</p>";
}

$conn->close();
?>

<style>
    .verify-button {
        margin-left: 10px;
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    .verify-button:hover {
        background-color: #0056b3;
    }
</style>
<script>
function approveApplication() {
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "approve_application.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert(xhr.responseText);  
            window.location.href = "av.php"; 
        }
    };
    
    
    xhr.send("action=approve");
}

</script>

<script>
function rejectApplication() {
    
    var rejectModal = document.getElementById('rejectModal');
    rejectModal.style.display = "block";

   
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "reject_form.html", true);  
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            rejectModal.innerHTML = xhr.responseText; 
            loadExistingRejectionData(); 
        }
    };
    xhr.send();
}

function loadExistingRejectionData() {
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_rejection_data.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.rejectReason) {
                
                document.getElementById('rejectReasonText').value = response.rejectReason;
            }
            if (response.errorType) {
                
                document.getElementById('errorType').value = response.errorType;
            }
        }
    };

    
    xhr.send("action=fetchRejectData");
}
function closeModal() {
    var rejectModal = document.getElementById('rejectModal');
    rejectModal.style.display = "none";
}

</script>
<style>


#rejectModal {
    display: none; 
    position: fixed;
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); 
    padding-top: 50px; 
    overflow: auto; 
}


#rejectModal .modal-content {
    background-color: #fff;
    margin: 5% auto; 
    padding: 20px;
    border-radius: 10px;
    width: 50%; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
}


#rejectModal h3 {
    text-align: center;
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
    font-weight: bold;
}


#rejectModal label {
    font-size: 16px;
    margin-bottom: 8px;
    display: block;
    color: #555;
}


#rejectReasonText {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 15px;
    resize: vertical;
}


#errorType {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 15px;
}


#rejectModal input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 15px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 18px;
    transition: background-color 0.3s ease;
}


#rejectModal input[type="submit"]:hover {
    background-color: #45a049;
}


#rejectModal .close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #f44336; 
    color: white;
    font-size: 18px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}


#rejectModal .close-btn:hover {
    background-color: #e53935;
}

</style>
