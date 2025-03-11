<?php
// db_connection.php
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "damanclub_db"; // Ensure this database exists in phpMyAdmin
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
