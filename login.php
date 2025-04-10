<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'databaseCooked.php';
$db = Database::dbConnect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo "Please enter both username and password.";
    } else {
        //checking to see if its an admin
        $stmt = $db->prepare("SELECT admin_id, password FROM admins WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'admin';
                header("Location: landingAdminPage.php"); 
                exit;
            }
        }

        $stmt = $db->prepare("SELECT user_id, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $message = "";
        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                header("Location: index.php");
                exit;
            } else {
                $message = "Invalid password. Please try again";
            }
        } else {
            $message = "User not found.<br><a href='create_user.php'>Create Account Here</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css" />
  <!-- the style.css was being stupid so i shoved all the formatting stuff in here lol -->
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Courier New', monospace;
      background-color: #f5f0e6;
    }

    .login-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #fff8ee;
      border-radius: 20px;
      padding: 2.5em;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 360px;
      text-align: center;
    }

    .logo {
      width: 240px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .login-box h2 {
      font-size: 1.8rem;
      margin-bottom: 1em;
      color: #5a4b3c;
    }

    .login-box label {
      display: block;
      font-size: 1rem;
      color: #5a4b3c;
      margin-top: 1em;
      margin-bottom: 0.5em;
    }

    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 0.75em;
      font-size: 1rem;
      border: 1px solid #d5c3ae;
      border-radius: 10px;
      background-color: #fff;
      margin-bottom: 1em;
    }

    .login-box input[type="submit"] {
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

    .login-box input[type="submit"]:hover {
      background-color: #d2c0ac;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <img src="photos/TheCookedMaster.png" alt="Cooked Book Logo" class="logo" />
      <div class="login-box">
        <?= $message ?>
        <h2>Login</h2>
        <form method="POST" action="login.php">
          <label for="username">Username:</label>
          <input type="text" name="username" id="username" required>

          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required>

          <input type="submit" value="Login">
        </form>
      </div>
    </div>
  </div>
</body>
</html>
