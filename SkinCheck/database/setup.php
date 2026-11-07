<?php

$host = "localhost";
$db_name = "skincheck_db";
$username = "root";
$password = ""; // Default XAMPP has no password

try {
    // First connect without database to create it
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "Database `$db_name` created/verified successfully.\n";

    // Connect to the new database
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read and execute schema.sql
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);
    echo "Schema executed successfully.\n";

    // Read and execute seed.sql
    $seed = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seed);
    echo "Seed data inserted successfully.\n";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
