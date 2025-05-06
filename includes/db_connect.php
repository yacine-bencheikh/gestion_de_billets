<?php
// Database connection parameters
$host = 'localhost';
$db_name = 'gestiontickets';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

// Create DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// Options for PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return rows as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);

    // Test query
    $testStmt = $pdo->query("SELECT 1");
    if (!$testStmt) {
        throw new PDOException("Connection test failed");
    }
    // echo "Connected successfully";
} catch (PDOException $e) {
    // Your existing error handling
}


?>