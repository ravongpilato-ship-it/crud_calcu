<?php
include "../config/db.php";

$id = (int)$_GET['id'];
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: ../index.php");
?>
