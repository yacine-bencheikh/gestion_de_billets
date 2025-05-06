<?php
session_start();
header('Content-Type: application/json');
echo json_encode([
    'sessionActive' => isset($_SESSION['id']),
    'userId' => $_SESSION['id'] ?? 'not set',
    'role' => $_SESSION['role'] ?? 'not set',
    'allSessionData' => $_SESSION
]);
?>