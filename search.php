<?php
include "config/db.php";

$term = $_GET['term'] ?? '';

if (!empty($term)) {
    $search_term = '%' . $term . '%';
    $stmt = $conn->prepare("SELECT name FROM users WHERE name LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    $names = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $names[] = $row['name'];
    }

    $stmt->close();
    echo json_encode($names); // send JSON back to JS
}
?>
