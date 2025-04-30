<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) {
    die("Recipe ID missing.");
}

// get recipe data
$stmt = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$recipe) {
    die("Recipe not found.");
}

// get all ingredients
$allIngredients = $db->query("SELECT * FROM ingredients ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// get recipe's current ingredients
$stmt = $db->prepare("SELECT ingredient_id FROM recipe_ingredients WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$usedIngredients = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'ingredient_id');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="css/styleADMINCRUD.css" />
    <style>
      .ingredient-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.5em 1.5em;
      }
      .ingredient-item {
        font-family: 'Courier New', monospace;
        display: flex;
        align-items: center;
        gap: 0.5em;
      }
    </style>
</head>
<body>
  <h2>Edit Recipe</h2>

  <form method="POST" action="adminUpdateRecipe.php">
    <input type="hidden" name="id" value="<?= $recipe['recipe_id'] ?>">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($recipe['name']) ?>" required><br><br>

    <label>Difficulty (1-5): </label><br>
    <input type="number" name="difficulty" value="<?= $recipe['difficulty'] ?>"><br><br>

    <label>Description:</label><br>
    <textarea name="description"><?= htmlspecialchars($recipe['description']) ?></textarea><br><br>

    <label>Instructions:</label><br>
    <textarea name="instructions"><?= htmlspecialchars($recipe['instructions']) ?></textarea><br><br>

    <fieldset>
      <legend>Ingredients:</legend>
      <div class="ingredient-grid">
        <?php foreach ($allIngredients as $ing): ?>
          <label class="ingredient-item">
            <input type="checkbox" name="ingredients[]" value="<?= $ing['ingredient_id'] ?>"
              <?= in_array($ing['ingredient_id'], $usedIngredients) ? 'checked' : '' ?>>
            <?= htmlspecialchars($ing['name']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset><br>

    <input type="submit" value="Update">
  </form>

  <div style="margin-top: 2em;">
    <a href="landingAdminPage.php" class="back-button">&larr; Back to Dashboard</a>
  </div>
</body>
</html>

