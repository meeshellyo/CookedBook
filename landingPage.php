<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$role = $_SESSION['role'] ?? 'guest';
$isLoggedIn = isset($_SESSION['username']);

if ($role === 'admin' && !isset($_GET['viewasuser'])) {
    header("Location: landingAdminPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>The Cooked Book</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header>
    <h1>The Cooked Book</h1>
    <div class="nav-buttons">
      <?php if ($isLoggedIn): ?>
        <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 
          (<?= htmlspecialchars($role) ?>)</span>
        <?php if ($role === 'admin'): ?>
          <a href="landingAdminPage.php">Admin Dashboard</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="create_user.php">Create Account</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>More for me!</h2>
      <ul>
        <?php 
          $enableButtons = $role === 'user' || $role === 'admin';
        ?>
        <li>
          <button <?= $enableButtons ? '' : 'disabled' ?> onclick="location.href='myIngredients.php'">My Ingredients</button>
        </li>
        <li>
          <button <?= $enableButtons ? '' : 'disabled' ?> onclick="location.href='myFavorites.php'">My Favorited Recipes</button>
        </li>
      </ul>

      <?php if (!$isLoggedIn): ?>
        <p class="note">Log in to access these features.</p>
      <?php endif; ?>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <h2>The Cooked Masters</h2>
      <div class="recipe-grid">
        <div class="card"><a href="userRecipes.php?creator=campbell">Campbell Furr</a></div>
        <div class="card"><a href="userRecipes.php?creator=joshua">Joshua J</a></div>
        <div class="card"><a href="userRecipes.php?creator=tyler">Tyler Tran</a></div>
        <div class="card"><a href="userRecipes.php?creator=michelle">Michelle Tra</a></div>
        <div class="card"><a href="all_recipes.php">All Recipes</a></div>
      </div>
    </main>
  </div>
</body>
</html>

