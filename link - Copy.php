<?php

require 'config.php';


$sql = "SELECT url FROM links ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    $row = $result->fetch_assoc();
    $last_url = $row['url'];

    $uri = $last_url . "/mfscan/";

    
} else {
   
}


$conn->close();
?>
