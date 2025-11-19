<?php
// Fill these with your **MariaDB** credentials (NOT your SSH password)
$DB_HOST = '127.0.0.1';   // use 127.0.0.1 on clabsql
$DB_USER = 'anmata';      // your confirmed MariaDB user (worked in CLI)
$DB_PASS = 'hKe+ta4twzdJvOkK';  // type exactly, no spaces
$DB_NAME = 'db_anmata';   // your schema name

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
  die('Connection failed: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}
?>