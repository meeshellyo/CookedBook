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
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header>
    <h1>The Cooked Book - Admin Dashboard</h1>
    <div class="nav-buttons">
      <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</span>
      <a href="landingPage.php?viewasuser=true">View as User</a>
      <a href="logout.php">Logout</a>
    </div>
  </header>

  <div class="container">
    <main class="main-content">
      <h2>Admin Controls</h2>
      <ul>
        <li><a href="createRecipe.php">Create New Recipe</a></li>
        <li><a href="manageUsers.php">Manage Users (idkidk)</a></li>
        <li><a href="manageRecipes.php">View All Recipes</a></li>
      </ul>

    </main>
  </div>
</body>
</html>
