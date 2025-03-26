<?php
// Database connection
require 'config.php';
// Fetch the last link from the table
$sql = "SELECT url FROM links ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Get the last link from the result
    $row = $result->fetch_assoc();
    $last_url = $row['url'];

    // The base URL to which we will append /mfscan/
    $uri = $last_url . "/mfscan/";

    // Output the final URL with the last link
   
    // Optionally, redirect to the new URL
    // header("Location: " . $uri);
    // exit;
} else {
   
}

// Close connection
$conn->close();
?>
