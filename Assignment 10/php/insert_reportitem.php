<?php
include '../db/db_connect.php';
$r=$_POST['reportid']; $i=$_POST['itemid'];
$sql="INSERT INTO ReportItem (ReportID,ItemID) VALUES ($r,$i)";
if(mysqli_query($conn,$sql)) echo "✅ Linked Report–Item.<br>";
else echo "❌ Error: ".mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
