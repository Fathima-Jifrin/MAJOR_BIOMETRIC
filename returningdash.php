<?php
session_start();

require 'config.php' ;

if (!isset($_SESSION['officer_id'])) {
    echo "<script>alert('Please log in to access your dashboard.'); window.location.href='adminlog.html';</script>";
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: adminlog.html");
    exit();
}

$officer_id= $_SESSION['officer_id']; 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returning Officer Dashboard</title>
  
</head>
<body>
  <header>
    <div class="logo">
      <img src="logo.png" alt="logo">
    </div>
    <h1>Returning Officer Dashboard</h1>
  </header>
  <main>
    <div class="left-container">
      <nav>
        <a href="returningdash.php">Dashboard Overview</a>
        <a href="vm.html">Voter Management</a>
        <a href="candidatedash.php">Candidate Management</a>
        <a href="online.php">Online Voting</a>
        <a href="#">Settings</a>
        <a href="profile1.php">Profile</a>
        <a href="?logout=true">Logout</a>
      </nav>
    </div>
    <div class="right-container">
      <div class="right-container">
    <section class="analytics">
        <h2>Election Statistics</h2><br>
        <div class="stats">
            <div class="stat-item">
                <h3>Total Voters</h3>
                <p>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS total_voters FROM election_voters");
                    $row = $result->fetch_assoc();
                    echo $row['total_voters'];
                    ?>
                </p>
            </div>
            <div class="stat-item">
                <h3>Total Candidates</h3>
                <p>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS total_candidates FROM candidates");
                    $row = $result->fetch_assoc();
                    echo $row['total_candidates'];
                    ?>
                </p>
            </div>
            <div class="stat-item">
                <h3>Online Voting Requests</h3>
                <p>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS pending_requests FROM online_voting_requests WHERE status='pending'");
                    $row = $result->fetch_assoc();
                    echo $row['pending_requests'];
                    ?>
                </p>
            </div>
            <div class="stat-item">
                <h3>Ongoing Elections</h3>
                <p>
                    <?php
                    $result = $conn->query("SELECT COUNT(*) AS ongoing_elections FROM elections WHERE election_date = CURDATE()");
                    $row = $result->fetch_assoc();
                    echo $row['ongoing_elections'];
                    ?>
                </p>
            </div>
        </div>
    </section>
</div>

    </div>
  </main>
  <footer>
    <p>&copy; 2024 Election Commission of India. All rights reserved.</p>
  </footer>
</body>
</html>

<style>

body {
    font-family: Arial, sans-serif;
    background-color: white;
    color: #ffffff;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}


header {
    display: flex;
    color: #fff;
    align-items: center;
    background-color: #112240;
    padding: 15px 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.logo img {
    width: 60px;
    height: 60px;
    margin-right: 15px;
}

h1 {
    font-size: 1.8rem;
    font-weight: bold;
    flex-grow: 1;
}


main {
    display: flex;
    flex: 1;
    height: 100%;
}

.left-container {
    flex: 0 0 260px; 
    background-color: #112240; 
    color: white;
    padding: 20px;
    min-height: 100vh; 
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1); 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.left-container nav a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 12px 15px;
    margin: 10px 0;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    transition: 0.3s;
}

.left-container nav a:hover {
    background: rgba(255, 255, 255, 0.3); 
    transform: translateX(5px);
}

.right-container {
    flex: 1;
    padding: 20px;
}


.analytics {
    background-color: #112240;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.analytics h2 {
    color: #64ffda;
    border-bottom: 2px solid #64ffda;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-item {
    background-color: #233554;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.stat-item h3 {
    margin-bottom: 10px;
    color: #64ffda;
}

.stat-item p {
    font-size: 20px;
    font-weight: bold;
}


footer {
    background-color: #112240;
    text-align: center;
    padding: 10px;
    margin-top: auto;
    position: fixed;
    bottom: 0;
    width: 100%;
}

</style>
