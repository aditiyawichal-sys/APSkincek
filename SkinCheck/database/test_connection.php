<?php

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Database connection successful!\n\n";

    try {
        // Test query
        $query = "SELECT id, name, email, role, created_at FROM users LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "--- Sample Users ---\n";
        foreach ($users as $user) {
            echo "ID: " . $user['id'] . "\n";
            echo "Name: " . $user['name'] . "\n";
            echo "Email: " . $user['email'] . "\n";
            echo "Role: " . $user['role'] . "\n";
            echo "Created At: " . $user['created_at'] . "\n";
            echo "--------------------\n";
        }
    } catch(PDOException $e) {
        echo "Query failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed.\n";
}
