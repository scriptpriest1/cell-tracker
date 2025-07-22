<?php
$servername = 'localhost';
$db_user = 'admin';
$db_password = 'great.admin';
$db_name = 'cell_tracker';

// Create connection
$conn = new mysqli($servername, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}
?>
