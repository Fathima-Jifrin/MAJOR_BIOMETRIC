<?php
session_start();

require 'config.php';


if (isset($_POST['submit'])) {
    $aadhaar_number = $_POST['aadhaar_number'];

    
    $target_dir = "uploads/";  
    $image_name = basename($_FILES["image"]["name"]);  
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION)); 
    $new_image_name = uniqid() . "." . $image_ext;  
    $target_file = $target_dir . $new_image_name;  

    
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($image_ext, $allowed_extensions)) {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
       
        $updateSQL = "UPDATE aadhaar_data SET image = ? WHERE aadhaar_number = ?";
        $stmt = $conn->prepare($updateSQL);
        $stmt->bind_param("ss", $target_file, $aadhaar_number);

        if ($stmt->execute()) {
            echo "Image uploaded and updated successfully!";
        } else {
            echo "Error updating database.";
        }

        $stmt->close();
    } else {
        echo "Error uploading the image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload User Image</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        form {
            border: 2px solid #ccc;
            padding: 20px;
            width: 50%;
            margin: auto;
            background: #f9f9f9;
        }
        select, input, button {
            padding: 10px;
            margin: 10px;
            width: 80%;
        }
    </style>
</head>
<body>

    <h2>Upload Image for a User</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Select User (Aadhaar Number):</label>
        <select name="aadhaar_number" required>
            <option value="">-- Select User --</option>
            <?php
            
            $sql = "SELECT aadhaar_number, full_name FROM aadhaar_data WHERE image IS NULL OR image = ''";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['aadhaar_number'] . "'>" . $row['full_name'] . " (" . $row['aadhaar_number'] . ")</option>";
            }
            ?>
        </select><br>

        <input type="file" name="image" accept="image/*" required><br>
        <button type="submit" name="submit">Upload Image</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>
