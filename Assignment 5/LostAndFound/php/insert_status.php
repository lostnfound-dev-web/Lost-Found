<?php
include '../db/db_connect.php';
$l=$_POST['label'];
$sql="INSERT INTO Status (Label) VALUES ('$l')";
if(mysqli_query($conn,$sql)) echo "✅ Status added.<br>";
else echo "❌ Error: ".mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
