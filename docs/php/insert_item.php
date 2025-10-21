<?php
include '../db/db_connect.php';
$n=$_POST['name']; $c=$_POST['category']; $d=$_POST['description'];
$p=$_POST['photo']; $dt=$_POST['datelost']; $l=$_POST['locationlost'];
$sql="INSERT INTO Item (Name,Category,Description,Photo,DateLost,LocationLost)
      VALUES ('$n','$c','$d','$p','$dt','$l')";
if(mysqli_query($conn,$sql)) echo "✅ Item added.<br>";
else echo "❌ Error: ".mysqli_error($conn);
echo '<a href="../maintenance.html">Back</a>';
mysqli_close($conn);
?>
