<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = '127.0.0.1';       // important: 127.0.0.1 (not localhost)
$user = 'anmata';          // the user that worked in mysql CLI
$pass = 'REPLACE_WITH_YOUR_DB_PASSWORD';  // paste exact DB password
$db   = 'db_anmata';

echo "<pre>test_db.php is reachable ✅\n";
echo "host=$host user=$user db=$db\n";

$conn = @mysqli_connect($host, $user, $pass, $db, 3306);
if (!$conn) {
  echo "CONNECT FAIL ❌ (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . "\n";
  exit;
}
echo "CONNECTED ✅\n";
$r = mysqli_query($conn, "SELECT 1 AS ok");
$row = mysqli_fetch_assoc($r);
echo "SELECT 1 => " . $row['ok'] . "\n";
mysqli_close($conn);
echo "</pre>";
?>