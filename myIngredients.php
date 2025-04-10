
<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = Database::dbConnect();
$stmt = $conn->query("SELECT ingredient_id, name FROM ingredients ORDER BY name ASC");
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>My Ingredients</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css">
</head>
<body>
  <h2>Select Your Ingredients</h2>

  <form action="ingredientResults.php" method="POST">
    <div class="ingredient-list" style="columns: 2; max-width: 600px; margin: auto;">
      <?php foreach ($ingredients as $ingredient): ?>
        <label>
          <input type="checkbox" name="ingredients[]" value="<?= $ingredient['ingredient_id'] ?>">
          <?= htmlspecialchars($ingredient['name']) ?>
        </label><br>
      <?php endforeach; ?>
    </div>
    <br>
    <div style="text-align:center;">
      <input type="submit" value="Find Recipes" class="action-link">
    </div>
  </form>

  <div style="text-align:center;">
    <a href="index.php" class="back-button">← Back to Main Page</a>
  </div>
</body>
</html>
