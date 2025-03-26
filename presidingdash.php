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
  
</head>
<body>
  <header>
    <div class="logo">
      <img src="logo.png" alt="logo">
    </div>
    <h1>Presiding Officer Dashboard</h1>
  </header>
  <main>
    <div class="left-container">
      <nav>
        <a href="presidingdash.php">Dashboard Overview</a>
        <a href="electdash.html">Election Management</a>
        <a href="voting/result_dash.php">Result Management</a>
        <a href="ongoing_elections.php">Analytics</a>
        <a href="#">Settings</a>
        <a href="profile1.php">Profile</a>
        <a href="?logout=true">Logout</a>
      </nav>
    </div>
    <div class="right-container">
      <section class="election-details">
    <h2>Election Details</h2>

    
    <div class="election-category">
        <h3>Past Elections</h3>
        <div class="election-list">
            <?php
            $result = $conn->query("SELECT * FROM elections WHERE online_voting='deactivate'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='election-card past'>{$row['election_name']}</div>";
                }
            } else {
                echo "<p class='no-elections'>No past elections.</p>";
            }
            ?>
        </div>
    </div>

    
    <div class="election-category">
        <h3>Ongoing Elections</h3>
        <div class="election-list">
            <?php
            $result = $conn->query("SELECT * FROM elections WHERE online_voting='active'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='election-card ongoing'>{$row['election_name']}</div>";
                }
            } else {
                echo "<p class='no-elections'>No ongoing elections.</p>";
            }
            ?>
        </div>
    </div>

    
    <div class="election-category">
        <h3>Upcoming Elections</h3>
        <div class="election-list">
            <?php
            $result = $conn->query("SELECT * FROM elections WHERE online_voting='inactive'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='election-card upcoming'>{$row['election_name']}</div>";
                }
            } else {
                echo "<p class='no-elections'>No upcoming elections.</p>";
            }
            ?>
        </div>
    </div>
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
    background: linear-gradient(135deg, #1e3c72, #2a5298); 
    color: #fff;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); 
}

.logo img {
    width: 60px;
    height: 60px;
    margin-right: 15px;
}

h1 {
    font-size: 1.8rem;
    font-weight: bold;
}


main {
    display: flex;
    flex: 1;
    height: 100%;
}
.left-container {
    flex: 0 0 260px; 
    background: linear-gradient(135deg, #1e3c72, #2a5298); 
    color: white;
    padding: 20px;
    min-height: 100vh; 
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1); 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

nav {
    display: flex;
    flex-direction: column;
    gap: 12px; 
}

nav a {
    text-decoration: none;
    color: white;
    padding: 12px 15px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1); 
    transition: all 0.3s ease;
    font-weight: 500;
}

nav a:hover {
    background: rgba(255, 255, 255, 0.3); 
    transform: translateX(5px); 
}

nav a:last-child {
    margin-top: auto; 
    background: rgba(255, 0, 0, 0.7); 
}

nav a:last-child:hover {
    background: rgba(255, 0, 0, 1);
}

.right-container {
    flex: 1;
    padding: 20px;
}
.election-details {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.2s ease-in-out;
}

.election-details:hover {
    transform: translateY(-3px);
}

.election-details h2 {
    color: #0b3d91;
    font-size: 1.8em;
    font-weight: bold;
    margin-bottom: 15px;
    text-transform: uppercase;
    border-bottom: 3px solid #0b3d91;
    padding-bottom: 5px;
}

.election-category {
    margin-top: 15px;
}

.election-category h3 {
    color: #333;
    font-size: 1.4em;
    font-weight: 600;
    margin-bottom: 10px;
    padding-left: 5px;
    border-left: 4px solid #0b3d91;
}

.election-list {
    display: flex;
    flex-direction: column; 
    gap: 10px;
}

.election-card {
    background: linear-gradient(135deg, #4CAF50, #2E7D32);
    color: white;
    padding: 12px;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
    width: 100%; 
}

.election-card.past {
    background: linear-gradient(135deg, #FF6F61, #D84315);
}

.election-card.ongoing {
    background: linear-gradient(135deg, #FFD54F, #FF8F00);
}

.election-card.upcoming {
    background: linear-gradient(135deg, #64B5F6, #1565C0);
}

.no-elections {
    color: #888;
    font-style: italic;
    text-align: center;
    font-weight: 500;
}

@media (max-width: 768px) {
    .election-list {
        flex-direction: column;
    }

    .election-card {
        width: 100%;
    }
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
