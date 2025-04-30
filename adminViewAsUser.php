<?php
session_start();
require_once("databaseCooked.php");

$conn = Database::dbConnect();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// create temp id
$tempUsername = 'admin_as_user_' . $_SESSION['admin_id'];

// see if this user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$tempUsername]);
$tempUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tempUser) {
    ///creates temp account
    $insert = $conn->prepare("
        INSERT INTO users (username, email, password)
        VALUES (?, ?, ?)
    ");
    $insert->execute([
        $tempUsername,
        $tempUsername . '@example.com',
        password_hash('temporary', PASSWORD_DEFAULT)
    ]);
    $tempUserId = $conn->lastInsertId();
} else {
    $tempUserId = $tempUser['user_id'];
}

// now make SESSION to act exactly like a user
$_SESSION['original_role'] = $_SESSION['role']; 
$_SESSION['role'] = 'user'; 
$_SESSION['user_id'] = $tempUserId;
$_SESSION['viewasuser'] = true;

// main page
header("Location: index.php");
exit();
?>
