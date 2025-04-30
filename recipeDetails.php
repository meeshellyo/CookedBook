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

$stmt = $conn->prepare("SELECT r.*, a.username AS creator FROM recipes r JOIN admins a ON r.admin_id = a.admin_id WHERE r.recipe_id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
  die("Recipe not found.");
}

$ingredientStmt = $conn->prepare("
  SELECT i.name, ri.quantity
  FROM recipe_ingredients ri
  JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
  WHERE ri.recipe_id = ?
");
$ingredientStmt->execute([$recipe['recipe_id']]);
$ingredients = $ingredientStmt->fetchAll(PDO::FETCH_ASSOC);

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

    .recipe-back-buttons {
      margin-top: 2em;
      display: flex;
      gap: 1em;
      flex-wrap: wrap;
    }

    .back-button {
      background-color: #ffeedd;
      color: #5a4b3c;
      border: 1px solid #ffccb3;
      padding: 0.6em 1.2em;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .back-button:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
      transform: scale(1.05);
    }

    .recipe-detail-container {
      padding: 2em;
      max-width: 800px;
      margin: 0 auto;
      background-color: #fff8ee;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }

    .recipe-title {
      font-size: 2rem;
      margin-bottom: 0.5em;
      color: #5a4b3c;
    }

    .recipe-meta {
      font-size: 1rem;
      color: #6a5d4d;
    }

    .recipe-section {
      margin-top: 1.5em;
    }

    .recipe-section h3 {
      font-size: 1.3rem;
      color: #4a392e;
    }

    .recipe-section p {
      font-size: 1rem;
      color: #554d43;
    }

    .ingredient-list {
      display: flex;
      flex-direction: column;
      gap: 0.6em;
      margin-top: 1em;
    }

    .ingredient-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 0.5em;
      padding: 0.2em 0;
    }

    .ingredient-label {
      font-size: 1rem;
      color: #554d43;
    }

    .ingredient-form {
      display: inline;
      margin: 0;
      padding: 0;
      background: none;
      border: none;
      box-shadow: none;
    }

    .add-cart-button {
      background-color: #e5f9e7;
      color: #2b6e3f;
      border: 1px solid #90d29c;
      padding: 0.25em 0.6em;
      border-radius: 6px;
      font-size: 0.75rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .add-cart-button:hover {
      background-color: #c8f3cf;
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
        <div class="ingredient-list">
          <?php foreach ($ingredients as $ing): ?>
            <div class="ingredient-row">
              <div class="ingredient-label">
                <?= htmlspecialchars($ing['name']) ?> <?= $ing['quantity'] ? '(' . htmlspecialchars($ing['quantity']) . ')' : '' ?>
              </div>

              <?php if (isset($_SESSION['user_id'])): ?>
                <form action="addToCart.php" method="POST" class="ingredient-form">
                  <input type="hidden" name="ingredient_name" value="<?= htmlspecialchars($ing['name']) ?>">
                  <input type="hidden" name="quantity" value="<?= htmlspecialchars($ing['quantity']) ?>">
                  <button type="submit" class="add-cart-button">Add to Cart</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="recipe-section">
      <h3>Instructions</h3>
      <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
    </div>

    <div class="recipe-back-buttons">
    <?php
    $mainBackLink = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && !isset($_SESSION['viewasuser']))
        ? 'landingAdminPage.php'
        : 'index.php';
    ?>
    <a href="<?= $mainBackLink ?>" class="back-button">← Return to Main Page</a>
      <a href="<?= $backLink ?>" class="back-button">
        ← Return to <?= 
          ($source === 'userRecipes' && $creator) ? htmlspecialchars($creator) . "'s Recipes" : 
          ($source === 'myFavorites' ? "My Favorites" : 
          ($source === 'myIngredients' ? "My Ingredients Results" : 
          "All Recipes")) 
        ?>
      </a>
    </div>
  </div>
</body>
</html>

