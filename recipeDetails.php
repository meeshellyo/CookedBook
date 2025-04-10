<?php
session_start();
require_once("databaseCooked.php");
$conn = Database::dbConnect();

$id = $_GET['id'] ?? null;
$source = $_GET['source'] ?? 'all_recipes';
$creator = $_GET['creator'] ?? null;

if (!$id) {
  die("Recipe not found.");
}

// Get recipe
$stmt = $conn->prepare("SELECT r.*, a.username AS creator FROM recipes r JOIN admins a ON r.admin_id = a.admin_id WHERE r.recipe_id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
  die("Recipe not found.");
}

// Get ingredients for the recipe
$ingredientStmt = $conn->prepare("
  SELECT i.name, ri.quantity
  FROM recipe_ingredients ri
  JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
  WHERE ri.recipe_id = ?
");
$ingredientStmt->execute([$recipe['recipe_id']]);
$ingredients = $ingredientStmt->fetchAll(PDO::FETCH_ASSOC);

// Set the back link
if ($source === 'userRecipes' && $creator) {
  $backLink = "usersRecipes.php?creator=" . urlencode($creator);
} elseif ($source === 'myFavorites') {
  $backLink = "myFavorites.php";
} elseif ($source === 'myIngredients') {
  $backLink = "ingredientResults.php";
} else {
  $backLink = "all_recipes.php";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($recipe['name']) ?></title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css" />
  <style>
    .title-with-button {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1em;
    }

    .favorite-inline-form {
      margin: 0;
    }

    .favorite-button {
      background-color: #ffeedd;
      color: #5a4b3c;
      border: 1px solid #ffccb3;
      padding: 0.25em 0.6em;
      font-size: 0.8rem;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .favorite-button:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
      transform: scale(1.05);
    }

    .favorite-form {
      background: none;
      box-shadow: none;
      padding: 0;
      margin: 0.5em 0 1em;
    }
  </style>
</head>
<body>
  <div class="recipe-detail-container">
    <div class="title-with-button">
      <h2 class="recipe-title"><?= htmlspecialchars($recipe['name']) ?></h2>

      <?php if (isset($_SESSION['user_id'])): 
        $userId = $_SESSION['user_id'];
        $stmtFav = $conn->prepare("SELECT * FROM users_favorites WHERE user_id = ? AND recipe_id = ?");
        $stmtFav->execute([$userId, $recipe['recipe_id']]);
        $isFavorited = $stmtFav->fetch();
      ?>
        <form action="<?= $isFavorited ? 'unfavorite_recipe.php' : 'favorite_recipe.php' ?>" method="POST" class="favorite-inline-form">
          <input type="hidden" name="recipe_id" value="<?= $recipe['recipe_id'] ?>">
          <input type="hidden" name="source" value="<?= htmlspecialchars($source) ?>">
          <input type="hidden" name="creator" value="<?= htmlspecialchars($creator) ?>">
          <button type="submit" class="favorite-button">
            <?= $isFavorited ? 'Unfavorite' : '❤ Favorite' ?>
          </button>
        </form>
      <?php endif; ?>
    </div>

    <p class="recipe-meta"><strong>Creator:</strong> <?= htmlspecialchars($recipe['creator']) ?></p>
    <p class="recipe-meta"><strong>Difficulty:</strong> <?= htmlspecialchars($recipe['difficulty']) ?></p>

    <div class="recipe-section">
      <h3>Description</h3>
      <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
    </div>


    <?php if (!empty($ingredients)): ?>
      <div class="recipe-section">
        <h3>Ingredients</h3>
        <ul>
          <?php foreach ($ingredients as $ing): ?>
            <li><?= htmlspecialchars($ing['name']) ?> <?= $ing['quantity'] ? '(' . htmlspecialchars($ing['quantity']) . ')' : '' ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

        <div class="recipe-section">
      <h3>Instructions</h3>
      <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
    </div>

    <a href="<?= $backLink ?>" class="back-button">← Back</a>
  </div>
</body>
</html>


