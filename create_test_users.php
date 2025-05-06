<?php
require_once 'includes/db_connect.php';

// Sample user data
$users = [
    [
        'name' => 'Client User',
        'email' => 'client@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'client'
    ],
    [
        'name' => 'Organizer User',
        'email' => 'organizer@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'organizer'
    ]
];

// Insert users into database
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

foreach ($users as $user) {
    try {
        $stmt->execute([
            $user['name'],
            $user['email'],
            $user['password'],
            $user['role']
        ]);
        echo "Created user: " . $user['email'] . "<br>";
    } catch (PDOException $e) {
        echo "Error creating user " . $user['email'] . ": " . $e->getMessage() . "<br>";
    }
}

echo "Done creating test users!";
?>