<?php
require_once 'databaseCooked.php';

$conn = Database::dbConnect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['new-username']);
    $email = trim($_POST['new-email']);
    $password = trim($_POST['new-password']);
    $confirm_pw = trim($_POST['confirm-password']);

    if ($password !== $confirm_pw) {
        die("Passwords do not match. <a href='create_user.php'>Go back</a>");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        echo "Account created successfully! <a href='login.php'>Login here</a>";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
            echo "That username is already taken. <a href='create_user.php'>Try again</a>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }

}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .signup-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        <form action="create_user.php" method="POST">
            <label for="new-username">Username:</label>
            <input type="text" name="new-username" required>
            
            <label for="new-email">ema:</label>
            <input type="text" name="new-email" required>    

            <label for="new-password">Password:</label>
            <input type="password" name="new-password" required>
            
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" name="confirm-password" required>
            
            <input type="submit" value="Create Account">
        </form>
    </div>
</body>
</html>
