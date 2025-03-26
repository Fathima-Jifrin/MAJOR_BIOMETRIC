<?php
session_start(); // Start the session
require 'config.php';

// Assuming $conn is your database connection, make sure it's initialized before executing queries
// Assuming $fingerprint_data and $aadhaar_number are already set with values

$fingerprint_data=$_SESSION['finger'] ;
$aadhaar_number=$_SESSION['aadhaar_number'] ;

// Update query to store fingerprint data in the database
$update_sql = "UPDATE aadhaar_data SET index_f = '$fingerprint_data' WHERE aadhaar_number = '$aadhaar_number'";

// Execute the query
if ($conn->query($update_sql) === TRUE) {
    // If update is successful, redirect to check.php with a success message
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Success</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; background: #111; color: #3fefef; }
            h1 { font-size: 2.5em; margin-top: 50px; }
        </style>
        <meta http-equiv="refresh" content="2;url=check.php">
    </head>
    <body>
        <h1>✅ Fingerprint Scanned Successfully!</h1>
    </body>
    </html>';
} else {
    // If update fails, show an error message
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; background: #111; color: #ff4444; }
            h1 { font-size: 2.5em; margin-top: 50px; }
        </style>
    </head>
    <body>
        <h1>❌ Error Updating Fingerprint!</h1>
    </body>
    </html>';
}
?>
