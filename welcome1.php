<?php
session_start();


require 'config.php' ;



$officer_id = $_SESSION['officer_id'];
$officer_type = strtolower($_SESSION['officer_type']);
$sql = "SELECT designation FROM election_officers WHERE officer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $officer_id);
$stmt->execute();
$stmt->bind_result($designation);
$stmt->fetch();
$stmt->close();
$conn->close();



$redirect_url = '';
if ($designation == $officer_type && $designation == 'presiding officer') {
    $redirect_url = 'presidingdash.php';
} elseif ($designation ==  $officer_type && $designation =='polling officer') {
    $redirect_url = 'pollingdash.php';
} elseif ($designation == $officer_type && $designation =='returning officer') {
    $redirect_url = 'returningdash.php';
} else {
    echo $officer_type ;
 echo"
 
<script>
  alert('invalid officer credentials');
  window.location.href = 'adminlog.html';
</script>


 ";

}



$encrypted_data = base64_encode(json_encode(['officer_id' => $officer_id]));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Voter's Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            flex-direction: column;
        }
        .message {
            font-size: 24px;
            color: #333;
            animation: fadeIn 2s ease-out;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
    <script>
        setTimeout(function () {
            window.location.href = '<?php echo $redirect_url; ?>?data=<?php echo $encrypted_data; ?>'; 
        }, 3000); 
    </script>
</head>
<body>
    <img src="logo.png" alt="Election Commission Logo" class="logo"> 
    <div class="message">Welcome to <?php echo "$designation";?> Dashboard!</div>
</body>
</html>
