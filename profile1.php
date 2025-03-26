<?php
session_start();

require 'config.php' ;

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access your profile.'); window.location.href='login.php';</script>";
    exit();
}

$officer_id = $_SESSION['officer_id']; 

$stmt = $conn->prepare("SELECT * FROM election_officers WHERE officer_id = ?");
$stmt->bind_param("i", $officer_id);
$stmt->execute();
$result = $stmt->get_result();
$officer = $result->fetch_assoc();

if (!$officer) {
    echo "<script>alert('Officer not found.'); window.location.href='login.php';</script>";
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: adminlog.html");
    exit();
}

$dashboard_page = "#";
switch (strtolower($officer['designation'])) {
    case 'returning officer':
        $dashboard_page = "returningdash.php";
        break;
    case 'polling officer':
        $dashboard_page = "pollingdash.php";
        break;
    case 'presiding officer':
        $dashboard_page = "presidingdash.php";
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Election Officer</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="logo">
        </div>
        <h1>Election Officer Profile</h1>
    </header>
    <main>
        <div class="nav">
            <a href="<?php echo $dashboard_page; ?>">Dashboard</a>
            <a href="?logout=true">Logout</a>
        </div>
        <section class="profile-info">
            <h2>Profile Information</h2>
            <p class="highlight"><strong>Officer ID:</strong> <?php echo htmlspecialchars($officer['officer_id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($officer['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($officer['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($officer['phone_number']); ?></p>
            <p><strong>Aadhaar:</strong> <?php echo htmlspecialchars($officer['aadhaar']); ?></p>
            <p class="highlight"><strong>Constituency:</strong> <?php echo htmlspecialchars($officer['con']); ?></p>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
    </footer>
</body>
</html>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

header {
    background-color: #0b3d91;
    color: #fff;
    padding: 10px;
    display: flex;
    align-items: center;
    height: 80px;
}

.logo img {
    width: 60px;
    height: 60px;
    margin-right: 10px;
}

h1 {
    padding-bottom: 5px;
    font-size: 24px;
}

main {
    flex: 1;
    padding: 20px;
    display: flex;
    justify-content: center;
}
.nav {
            position: absolute;
            right: 20px;
            top: 15px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .nav a:hover {
            background-color: #FF9933; 
        }

.profile-info {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px; 
    width: 100%;
}

.profile-info h2 {
    margin-bottom: 15px;
    font-size: 22px;
    color: #333;
}

.profile-info p {
    margin-bottom: 10px;
    line-height: 1.5;
}


.highlight {
    background-color: #e0f7fa; 
    border-left: 4px solid #0097a7; 
    padding: 10px;
    border-radius: 4px;
}

footer {
    text-align: center;
    padding: 20px 0;
    background-color: #333;
    color: white;
}


@media (max-width: 768px) {
    header {
        flex-direction: column;
        align-items: flex-start;
    }

    h1 {
        font-size: 20px;
    }

    .profile-info {
        padding: 15px;
    }

    .profile-info h2 {
        font-size: 20px;
    }

    .highlight {
        padding: 8px;
    }
}



</style>