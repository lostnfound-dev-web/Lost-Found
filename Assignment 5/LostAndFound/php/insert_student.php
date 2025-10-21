<?php
include '../db/db_connect.php';
$uid = $_POST['userid'];
$sql = "INSERT INTO Student (UserID) VALUES ('$uid')";
if (mysqli_query($conn, $sql)) echo "✅ Student added.<br>";
else echo "❌ Error: " . mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
