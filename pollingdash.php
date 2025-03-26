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
  <title>E-Voting Admin Dashboard</title>
  <link rel="stylesheet" href="ElectionOfficerdashboard.css">
</head>
<body>
  <header>
    <div class="logo">
      <img src="logo.png" alt="logo">
    </div>
    <h1>Polling Officer Dashboard</h1>
  </header>
  <main>
    <div class="left-container">
      <nav>
        <a href="pollingdash.php">Dashboard Overview</a>
        <a href="#">Voter Authentication Panel</a>
        <a href="#">Voter List & Status</a>
        <a href="#">Alerts & Notifications</a>
        <a href="#">System Logs & Reports</a>
        <a href="#">Security Features</a>
        <a href="#">Analytics</a>
        <a href="#">Settings</a>
        <a href="profile1.php">Profile</a>
        <a href="?logout=true">Logout</a>
      </nav>
    </div>
    <div class="right-container">
      <section class="analytics">
        <h2>Election Analytics</h2><br>
        <div class="stats">
          <div class="stat-item">
            <h3>Total Voters</h3>
            <p>1,245</p>
          </div>
          <div class="stat-item">
            <h3>Votes Cast</h3>
            <p>985</p>
          </div>
          <div class="stat-item">
            <h3>Turnout Percentage</h3>
            <p>79%</p>
          </div>
          <div class="stat-item">
            <h3>Ongoing Elections</h3>
            <p>3</p>
          </div>
        </div>
      </section>

      <section class="election-details">
        <h2>Election Details</h2><br>
        
        <ul>
          <li>Election 1 - Status: Ongoing</li>
          <li>Election 2 - Status: Scheduled for 15 Nov</li>
          <li>Election 3 - Status: Completed</li>
        </ul>
      </section>
    </div>
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
    font-family: Arial, sans-serif;
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
    height: 100%;
}
img {
    width: 60px;
    height: 60px;
    margin-right: 10px;
}
h1 {
    padding-bottom: 5px;
}
main {
    display: flex;
    flex: 1;
    height: 100%;
}
.left-container {
    flex: 0 0 250px; 
}
nav {
    display: flex;
    flex-direction: column;
    background-color: #f2f2f2;
    padding: 20px;
    height: 100%;
}
nav a {
    text-decoration: none;
    color: #333;
    padding: 10px;
    margin-bottom: 10px;
    background-color: #eaeaea;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}
nav a:hover {
    background-color: #ccc;
}
.right-container {
    flex: 1;
    padding: 20px;
}
.analytics {
    background-color: #f9f9f9;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.stats {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-around;
}
.stat-item {
    background-color: #4CAF50;
    padding: 15px;
    border-radius: 8px;
    width: 150px;
    text-align: center;
    color: #fff;
}
.stat-item h3 {
    margin-bottom: 10px;
}
.stat-item p {
    font-size: 1.5em;
}
.election-details {
    background-color: #e3f2fd;
    padding: 20px;
    border-radius: 5px;
}
.election-details ul {
    list-style: none;
    padding-left: 0;
}
.election-details li {
    padding: 5px 0;
    background-color: #bbdefb;
    margin-bottom: 5px;
    border-radius: 5px;
    padding-left: 10px;
}
footer {
    text-align: center;
    padding: 20px 0;
    background-color: #333;
    color: white;
    position: fixed;
    bottom: 0;
    width: 100%;
}


@media (max-width: 768px) {
    main {
        flex-direction: column;
    }
    .left-container {
        flex: 0 0 100%;
    }
    .right-container {
        flex: 1;
    }
    .stats {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
