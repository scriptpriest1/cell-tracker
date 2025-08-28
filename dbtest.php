<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is running<br>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=test", "root", "");
    echo "✅ Connection successful!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
