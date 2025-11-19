<?php
header("Content-Type: application/json");
require_once "db_connect.php";

$tags = [];

$sql = "SELECT DISTINCT Name FROM Item";
$res = mysqli_query($conn, $sql);

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $tags[] = $row['Name'];
    }
}

echo json_encode($tags);
?>
