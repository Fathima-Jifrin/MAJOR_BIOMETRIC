<?php

require 'config.php' ;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw'])) {
    $candidate_id = $_POST['candidate_id'];
    $withdrawal_reason = $_POST['withdrawal_reason']; 

    
    $update_sql = "UPDATE candidates SET verified = 'Withdrawn', withdrawal_reason = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('si', $withdrawal_reason, $candidate_id);
    
    if ($stmt->execute()) {
        $message = "Candidate has been successfully withdrawn.";
    } else {
        $message = "Error: Could not withdraw the candidate.";
    }
}


$search_results = [];
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $search_type = $_POST['search_type'];

    $sql = "SELECT * FROM candidates WHERE $search_type LIKE ? AND verified != 'Withdrawn'";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param('s', $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Candidate - Election Commission</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    
    <div class="main-content">
        
        
        <header class="header">
            <h1>Candidate Withdrawal</h1>
            <a href="returningdash.php" class="back-button">← Back to Dashboard</a>
        </header>

        
        <?php if (isset($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        
        <section class="search-section">
            <h2>Search Candidates</h2>
            <form method="POST" action="withdraw_candidate.php">
                <div class="search-form">
                    <label for="search_query">Search by:</label>
                    <select name="search_type" id="search_type" required>
                        <option value="election_id_number">Election ID</option>
                        <option value="aadhaar_number">Aadhaar Number</option>
                        <option value="name">Candidate Name</option>
                    </select>
                    <input type="text" name="search_query" id="search_query" placeholder="Enter search value..." required>
                    <button type="submit" name="search" class="btn-search">Search</button>
                </div>
            </form>
        </section>

        
        <?php if (!empty($search_results)) { ?>
            <section class="search-results">
                <form method="POST" action="withdraw_candidate.php">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Party</th>
                                <th>Aadhaar</th>
                                <th>Election ID</th>
                                <th>Withdrawal Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $row) { ?>
                                <tr>
                                    <td><img src="<?php echo $row['candidate_image']; ?>" alt="Candidate Image" class="candidate-img"></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['party']; ?></td>
                                    <td><?php echo $row['aadhaar_number']; ?></td>
                                    <td><?php echo $row['election_id_number']; ?></td>
                                    <td>
                                        
                                        <textarea name="withdrawal_reason" placeholder="Enter reason..." rows="3" required></textarea>
                                    </td>
                                    <td>
                                        <input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="withdraw" class="btn-withdraw">Withdraw</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>
            </section>
        <?php } ?>
    </div>

    
    <footer>
        <p>&copy; 2024 Election Commission of India. All Rights Reserved.</p>
    </footer>

</body>
</html>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    color: #333;
    display: flex;
    flex-direction: column;
    height: 100vh;
}


.main-content {
    padding: 40px;
    width: 100%;
    margin: 0 auto;
    max-width: 1200px;
}


.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header h1 {
    font-size: 30px;
    color: #003366;
}

.back-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
}

.back-button:hover {
    background-color: #0056b3;
}


.message {
    background-color: #28a745;
    color: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
}


.search-section {
    margin-bottom: 30px;
}

.search-form {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-form label,
.search-form input,
.search-form select {
    font-size: 16px;
    padding: 10px;
}

.search-form select,
.search-form input {
    width: 250px;
}

.search-form .btn-search {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 5px;
}

.search-form .btn-search:hover {
    background-color: #218838;
}


.search-results table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.search-results th,
.search-results td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
}

.search-results th {
    background-color: #003366;
    color: white;
}

.search-results td {
    background-color: #fff;
}

.search-results tr:hover {
    background-color: #f1f1f1;
}

.search-results textarea {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.search-results button {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
}

.search-results button:hover {
    background-color: #c82333;
}

footer {
    background-color: #003366;
    color: white;
    text-align: center;
    padding: 20px;
    margin-top: auto;
}

</style>