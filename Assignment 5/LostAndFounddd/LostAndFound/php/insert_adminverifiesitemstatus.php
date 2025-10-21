<?php
include '../db/db_connect.php';
$a=$_POST['adminid']; $i=$_POST['itemid']; $s=$_POST['statusid'];
$sql="INSERT INTO AdminVerifiesItemStatus (AdminID,ItemID,StatusID) VALUES ($a,$i,$s)";
if(mysqli_query($conn,$sql)) echo "✅ Verification recorded.<br>";
else echo "❌ Error: ".mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
