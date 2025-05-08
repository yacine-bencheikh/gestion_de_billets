<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = filter_input(INPUT_POST, 'loginEmail', FILTER_SANITIZE_EMAIL);
    $password = $_POST['loginPassword'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header('Location: login.html');
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Invalid email or password";
        header('Location: login.html');
        exit;
    }
    if($user['role'] === 'client'){
        header('Location: ../client_dashboard.php');
    }

    // Set session variables
    $_SESSION['id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];


    // Redirect based on role
    if ($user['role'] === 'organizer') {
        header('Location: ../organizer_dashboard.php');
    }
    exit;

} else {
    // Redirect if not a POST request
    header('Location: login.html');
    exit;
}
?>