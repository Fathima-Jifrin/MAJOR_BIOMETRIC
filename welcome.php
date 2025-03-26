<?php
session_start();
$encrypted_data = base64_encode(json_encode(['voter_id' => $_SESSION['voter_id']])); 
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
            window.location.href = 'dashboard.php?data=<?php echo $encrypted_data; ?>'; 
        }, 3000); 
    </script>
</head>
<body>
    <img src="logo.png" alt="Election Commission Logo" class="logo"> 
    <div class="message">Welcome to Voter's Portal!</div>
</body>
</html>
