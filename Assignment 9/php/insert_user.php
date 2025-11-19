<?php
include '../db/db_connect.php';
$email = $_POST['email'];
$password = $_POST['password'];
$sql = "INSERT INTO User (Email, Password) VALUES ('$email', '$password')";
if (mysqli_query($conn, $sql)) echo "✅ User added.<br>";
else echo "❌ Error: " . mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
