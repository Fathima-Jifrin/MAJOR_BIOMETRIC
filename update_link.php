<?php

require 'config.php' ;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'];
    $Link_name = $_POST['Link_name'];
    $url = $_POST['url'];

    if ($id) {
        
        $sql = "UPDATE links SET Link_name=?, url=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $Link_name, $url, $id);

        if ($stmt->execute()) {
            echo "<p>Record updated successfully</p>";
        } else {
            echo "<p>Error updating record: " . $stmt->error . "</p>";
        }

        
        $stmt->close();
    } else {
        
        $sql = "INSERT INTO links (Link_name, url) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $Link_name, $url);

        if ($stmt->execute()) {
            echo "<p>Record inserted successfully</p>";
        } else {
            echo "<p>Error inserting record: " . $stmt->error . "</p>";
        }

        
        $stmt->close();
    }
} else {
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT Link_name, url FROM links WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($link_name, $url);
        $stmt->fetch();

        
        $stmt->close();
    } else {
        echo "<p>No record found!</p>";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update or Insert Link</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .container {
            width: 50%;
            margin: 0 auto;
        }
        label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update or Insert Link Information</h2>
        <form method="POST" action="">
            <label for="Link_name">Link Name:</label>
            <input type="text" id="Link_name" name="Link_name" value="<?php echo isset($link_name) ? $link_name : ''; ?>" required>
            <label for="url">URL:</label>
            <input type="text" id="url" name="url" value="<?php echo isset($url) ? $url : ''; ?>" required>
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
