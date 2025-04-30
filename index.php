<?php
session_start();
require_once("databaseCooked.php");

if (isset($_GET['viewasuser'])) {
    $_SESSION['viewasuser'] = true; 
}

$role = $_SESSION['role'] ?? 'guest';
$isLoggedIn = isset($_SESSION['username']);
$actingAsUser = isset($_SESSION['viewasuser']) && $_SESSION['viewasuser'];

if ($role === 'admin' && !$actingAsUser) {
    header("Location: landingAdminPage.php");
    exit();
}

$conn = Database::dbConnect();



// Define chefs
$chefs = ['Campbell', 'Joshua', 'Tyler', 'Michelle'];

$imageMap = [
    'Campbell' => 'chefcamp.png',
    'Joshua' => 'chefjosh.png',
    'Tyler' => 'cheftt.png',
    'Michelle' => 'chefmichelle.png',
];

$bios = [
    'Campbell' => 'Hi Im Campbell, and I like to share some of my family recipes with the world. I hope you enjoy some southern specials like gumbo, hoppin john, and babas red beans!',
    'Joshua' => 'I am josh, thanks for checking out my recipes! I like to eat food, you also have to eat to live. Here are some recipes for you! Please cook with caution.',
    'Tyler' => 'Hey, I’m Tyler Tran—a college student by day and passionate home chef by night. Between classes and campus life, I find joy in the kitchen, creating everything from late-night comfort food to fun, flavor-packed meals that anyone can make. My recipes are all about simplicity, creativity, and sharing good food with good people.',
    'Michelle' => 'Hi!  I am Michelle and I prefer sweet treats and drinks oppossed to food!  Dont get me wrong, I still eat a lot.  Have fun checking out some of my creations!',
];

// Suggested recipes
$chefRecipes = [];
foreach ($chefs as $chef) {
    $stmt = $conn->prepare("
        SELECT recipe_id, name 
        FROM recipes 
        JOIN admins ON recipes.admin_id = admins.admin_id 
        WHERE admins.username = ? 
        ORDER BY recipe_id ASC 
        LIMIT 2
    ");
    $stmt->execute([$chef]);
    $chefRecipes[$chef] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>The Cooked Book</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css" />
  <style>
    .chef-card {
      display: flex;
      align-items: center;
      background-color: #fff8ee;
      border: 1px solid #d5c3ae;
      border-radius: 15px;
      padding: 1.5em;
      margin: 1.5em 0;
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .chef-card:hover {
      transform: scale(1.02);
    }

    .chef-card img {
      width: 120px;
      height: 120px;
      object-fit: contain;
      border-radius: 15px;
      margin-right: 1.5em;
    }

    .chef-card-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      max-width: 500px;
    }

    .chef-card-content h3 {
      margin: 0;
      font-size: 1.8rem;
      color: #5a4b3c;
    }

    .chef-card-content p {
      margin-top: 0.5em;
      font-size: 0.9rem;
      color: #7a6b5b;
    }

    .chef-card-recipes {
      display: flex;
      flex-direction: column;
      gap: 0.5em;
      min-width: 200px;
      margin-left: 2em;
      text-align: center;
    }

    .recipe-button {
      padding: 0.5em 1em;
      background-color: #ffeedd;
      border: 1px solid #ffccb3;
      border-radius: 8px;
      text-decoration: none;
      color: #5a4b3c;
      font-weight: bold;
      font-size: 0.9rem;
      transition: background-color 0.3s ease;
    }

    .recipe-button:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar button {
      margin-bottom: 1em;
    }

    .sidebar button.big-button {
      font-size: 1.2rem;
      padding: 0.75em 1.5em;
    }
  </style>
</head>
<body>
  <header>
    <h1>The Cooked Book</h1>
      <div class="nav-buttons">
        <?php if ($isLoggedIn): ?>
          <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($role) ?>)</span>

          <?php if (isset($_SESSION['viewasuser']) && $_SESSION['viewasuser']): ?>
            <a href="returnToAdmin.php" class="back-to-admin-button">Return to Admin</a>
          <?php elseif ($role === 'admin'): ?>
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
    <aside class="sidebar">
      <h2>More for me!</h2>

      <?php if (!$isLoggedIn): ?>
        <p class="note">Log in to access these features.</p>
      <?php endif; ?>

      <ul>
        <?php 
          $enableButtons = ($role === 'user' || ($role === 'admin' && $actingAsUser));
        ?>
        <li><button <?= $enableButtons ? '' : 'disabled' ?> onclick="location.href='myIngredients.php'">My Ingredients</button></li>
        <li><button <?= $enableButtons ? '' : 'disabled' ?> onclick="location.href='myFavorites.php'">My Favorited Recipes</button></li>
        <li><button <?= $enableButtons ? '' : 'disabled' ?> onclick="location.href='myShoppingCart.php'">My Shopping Cart</button></li>

        <li><button class="big-button" onclick="location.href='all_recipes.php'">All Recipes</button></li>
      </ul>
    </aside>

    <main class="main-content">
      <h2>The Cooked Masters</h2>

      <?php foreach ($chefs as $chef): ?>
        <div class="chef-card" onclick="location.href='usersRecipes.php?creator=<?= urlencode($chef) ?>'">
          <img src="photos/<?= $imageMap[$chef] ?>" alt="<?= htmlspecialchars($chef) ?>" />
          
          <div class="chef-card-content">
            <h3><?= htmlspecialchars($chef) ?></h3>
            <p><?= htmlspecialchars($bios[$chef]) ?></p>
          </div>

          <div class="chef-card-recipes">
            <strong>Suggested Recipes:</strong>
            <?php foreach ($chefRecipes[$chef] as $recipe): ?>
              <a href="recipeDetails.php?id=<?= $recipe['recipe_id'] ?>" class="recipe-button" onclick="event.stopPropagation();">
                <?= htmlspecialchars($recipe['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
      
    </main>
  </div>
</body>
</html>




