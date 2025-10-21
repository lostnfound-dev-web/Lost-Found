<?php
include '../db/db_connect.php';
$i=$_POST['itemid']; $s=$_POST['statusid'];
$sql="INSERT INTO ItemStatus (ItemID,StatusID) VALUES ($i,$s)";
if(mysqli_query($conn,$sql)) echo "✅ Linked Item–Status.<br>";
else echo "❌ Error: ".mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
