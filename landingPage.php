<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
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
    <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
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
  <li>
    <button <?= $isLoggedIn ? '' : 'disabled' ?> onclick="location.href='myIngredients.php'">My Ingredients</button>
  </li>
  <li>
    <button <?= $isLoggedIn ? '' : 'disabled' ?> onclick="location.href='myFavorites.php'">My Favorited Recipes</button>
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
        <div class="card">Campbell Furr</div>
        <div class="card">Joshua J</div>
        <div class="card">Tyler Tran</div>
        <div class="card">Michelle Tra</div>
        <div class="card">All Recipes</div>
      </div>
    </main>
  </div>
</body>
</html>

