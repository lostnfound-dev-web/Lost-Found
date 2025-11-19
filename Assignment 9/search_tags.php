<?php
header("Content-Type: application/json");
require_once "db_connect.php";

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = $_GET['q'];

$sql = "SELECT DISTINCT Name 
        FROM Item 
        WHERE Name LIKE CONCAT('%', ?, '%')
        LIMIT 10";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $q);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$tags = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tags[] = $row['Name'];
}

echo json_encode($tags);
?>
