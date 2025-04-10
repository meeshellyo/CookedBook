<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/style2.css" />
</head>
<body>
  <header>
    <h1>The Cooked Book - Our Admin Dashboard</h1>
    <div class="nav-buttons">
      <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</span>
      <a href="index.php?viewasuser=true">View as User</a>
      <a href="logout.php">Logout</a>
    </div>
  </header>

  <div class="dashboard-wrapper">
    <div class="dashboard-card">
      <h2>Admin Controls</h2>
      <ul>
        <li><a href="adminCreateRecipe.php">create new recipe</a></li>
        <li><a href="adminManageRecipe.php?action=edit">edit recipe</a></li>
        <li><a href="adminManageRecipe.php?action=delete">delete recipe</a></li>
        <li><a href="adminManageRecipe.php">view recipes</a></li>
        <li><a href="adminManageUsers.php">manage users??tbh idk what this is gonna be for lol</a></li>
      </ul>
    </div>
  </div>
</body>
</html>