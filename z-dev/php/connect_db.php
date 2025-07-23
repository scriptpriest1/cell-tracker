<?php
$servername = 'localhost';
$db_user = 'admin';
$db_password = 'great.admin';
$db_name = 'cell_tracker';

try {
  $conn = new PDO("mysql:host=$servername;dbname=$db_name;charset=utf8", $db_user, $db_password);
  // Set PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // Optional: Set default fetch mode to associative arrays
  $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die('Connection failed: ' . $e->getMessage());
}
?>
