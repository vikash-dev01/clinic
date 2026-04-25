<?php
$host = 'sql300.infinityfree.com';  // Correct host
$dbname = 'if0_41754166_clinic';
$username = 'if0_41754166';         // Removed space
$password = '7Mk8WO7VmzqGUM7';      // Removed space

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "Connected successfully"; // optional for testing
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>