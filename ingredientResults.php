<?php
session_start();
require_once("databaseCooked.php");

$conn = Database::dbConnect();

// handles POST and redirection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ingredients']) && is_array($_POST['ingredients'])) {
        $_SESSION['selected_ingredients'] = $_POST['ingredients']; //  save to session
    } else {
        $_SESSION['selected_ingredients'] = []; // clear session if no ingredients selected
    }
}

// Now pull from SESSION
$selected = $_SESSION['selected_ingredients'] ?? [];

$noIngredients = empty($selected);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Recipe Matches</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css">
  <style>
    .match-text {
      margin-top: 0.5em;
      font-weight: bold;
      font-size: 0.95rem;
      color: #4a392d;
    }

    .match-perfect {
      color: green;
    }

    .match-partial {
      color: orange;
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5em;
      font-size: 2rem;
      color: #5a4b3c;
    }

    .no-ingredients {
      text-align: center;
      margin-top: 4em;
      font-size: 1.2rem;
      color: #5a4b3c;
    }
  </style>
</head>
<body>

<?php if (!empty($noIngredients)): ?>
  <div class="no-ingredients">
    <p>No ingredients selected.</p>
    <a href="myIngredients.php" class="back-button">← Back to Ingredients</a>
  </div>
<?php else: ?>

  <h2>Recipe Matches Based on Your Pantry</h2>

  <?php
    // Prepare SQL and get recipes if ingredients were selected
    $placeholders = implode(',', array_fill(0, count($selected), '?'));
    $sql = "
      SELECT 
          r.recipe_id, r.name, r.difficulty, a.username AS creator,
          COUNT(ri.ingredient_id) AS total_ingredients,
          SUM(CASE WHEN ri.ingredient_id IN ($placeholders) THEN 1 ELSE 0 END) AS matched_ingredients
      FROM recipes r
      JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id
      JOIN admins a ON r.admin_id = a.admin_id
      GROUP BY r.recipe_id
      HAVING matched_ingredients > 0
      ORDER BY matched_ingredients DESC, total_ingredients ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($selected);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php if (!empty($recipes)): ?>
    <div class="recipe-grid">
      <?php foreach ($recipes as $recipe): 
        $isPerfectMatch = $recipe['matched_ingredients'] == $recipe['total_ingredients'];
        $matchClass = $isPerfectMatch ? 'match-perfect' : 'match-partial';
      ?>
        <a href="recipeDetails.php?id=<?= $recipe['recipe_id'] ?>&source=myIngredients" class="card-link">
          <div class="card">
            <h3><?= htmlspecialchars($recipe['name']) ?></h3>
            <p><strong>Difficulty:</strong> <?= htmlspecialchars($recipe['difficulty']) ?></p>
            <p><strong>Creator:</strong> <?= htmlspecialchars($recipe['creator']) ?></p>
            <p class="match-text <?= $matchClass ?>">
              <?php if ($isPerfectMatch): ?>
                ✅ You can make this!
              <?php else: ?>
                You have <?= $recipe['matched_ingredients'] ?>/<?= $recipe['total_ingredients'] ?> ingredients
              <?php endif; ?>
            </p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center;">No recipes found with your selected ingredients.</p>
  <?php endif; ?>

  <div style="text-align:center; margin-top: 2em;">
    <a href="myIngredients.php" class="back-button">← Back to Ingredients</a>
  </div>

<?php endif; ?>

</body>
</html>


