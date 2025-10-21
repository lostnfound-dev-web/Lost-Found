<?php
include '../db/db_connect.php';
$userid=$_POST['userid'];
$sql="INSERT INTO Report (UserID) VALUES ('$userid')";
if(mysqli_query($conn,$sql)) echo "✅ Report created.<br>";
else echo "❌ Error: ".mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
