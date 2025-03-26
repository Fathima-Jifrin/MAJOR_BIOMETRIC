
<?php
include('config.php');
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM candidates WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header("Location: candidates_list.php");
    exit;
}
?>
