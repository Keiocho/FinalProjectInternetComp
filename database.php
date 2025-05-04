<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection using PDO
$host = 'localhost';
$dbname = 'campuscart';
$username = 'root';  // 🔒 TODO: change to your new DB user like 'student'
$password = '';      // 🔒 TODO: set to that user's password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Connected successfully";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>
