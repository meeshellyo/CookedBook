<?php
session_start();
require_once 'databaseCooked.php';

$conn = Database::dbConnect();
$message = "";
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['new-username']);
  $email = trim($_POST['new-email']);
  $password = trim($_POST['new-password']);
  $confirm_pw = trim($_POST['confirm-password']);

  if ($password !== $confirm_pw) {
    $message = "Passwords do not match.<br>Please try again.";
  } else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    try {
      $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
      $stmt->bindParam(':username', $username);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':password', $hashedPassword);
      $stmt->execute();
      $message = "Account created successfully! One sec...  Redirecting to login...";
      $redirect = true;
    } catch (PDOException $e) {
      if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
        $message = "That username or email is already taken.<br>Please try again.";
      } else {
        $message = "Error: " . $e->getMessage();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Account</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Courier New', monospace;
      background-color: #f5f0e6;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .signup-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #fff8ee;
      border-radius: 20px;
      padding: 2.5em;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 380px;
      text-align: center;
    }

    .logo {
      width: 240px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .signup-box h2 {
      font-size: 1.8rem;
      margin-bottom: 1em;
      color: #5a4b3c;
    }

    .signup-box label {
      display: block;
      font-size: 1rem;
      color: #5a4b3c;
      margin-top: 1em;
      margin-bottom: 0.5em;
      text-align: left;
    }

    .signup-box input[type="text"],
    .signup-box input[type="password"] {
      width: 100%;
      padding: 0.75em;
      font-size: 1rem;
      border: 1px solid #d5c3ae;
      border-radius: 10px;
      background-color: #fff;
      margin-bottom: 1em;
    }

    .signup-box input[type="submit"] {
      width: 100%;
      padding: 0.8em;
      font-size: 1.1rem;
      background-color: #e0d5c3;
      color: #5a4b3c;
      border: none;
      border-radius: 12px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .signup-box input[type="submit"]:hover {
      background-color: #d2c0ac;
    }

    .message {
      margin-bottom: 1em;
      font-weight: bold;
      color: black;
    }
  </style>
  <?php if ($redirect): ?>
    <meta http-equiv="refresh" content="2;url=login.php">
  <?php endif; ?>
</head>
<body>
  <div class="signup-card">
    <img src="photos/TheCookedMaster.png" alt="Cooked Book Logo" class="logo" />
    <div class="signup-box">
      <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
      <?php endif; ?>
      <h2>Create Account</h2>
      <form action="create_user.php" method="POST">
        <label for="new-username">Username:</label>
        <input type="text" name="new-username" id="new-username" required>

        <label for="new-email">Email:</label>
        <input type="text" name="new-email" id="new-email" required>

        <label for="new-password">Password:</label>
        <input type="password" name="new-password" id="new-password" required>

        <label for="confirm-password">Confirm Password:</label>
        <input type="password" name="confirm-password" id="confirm-password" required>

        <input type="submit" value="Create Account">
      </form>
    </div>
  </div>
</body>
</html>

