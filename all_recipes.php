<?php
//this page loads up all of the recipes
session_start();
require_once("databaseCooked.php");

$conn = Database::dbConnect();

$stmt = $conn->query("
    SELECT r.recipe_id, r.name, r.difficulty, a.username AS creator
    FROM recipes r
    JOIN admins a ON r.admin_id = a.admin_id
    ORDER BY r.recipe_id DESC
");

$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>All Recipes</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css">
  <style>
    .recipe-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 2em;
      padding-top: 2em;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
    }

    .card {
      background-color: #fff8ee;
      padding: 1.5em;
      border-radius: 15px;
      border: 1px solid #ffccb3;
      text-align: center;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s ease;
    }

    .card:hover {
      transform: scale(1.03);
      background-color: #fff3e6;
    }

    .card h3 {
      font-size: 1.3rem;
      margin-bottom: 0.5em;
    }

    .card p {
      font-size: 1rem;
      margin: 0.4em 0;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center;">All Recipes</h2>

  <?php if (!empty($recipes)): ?>
    <div class="recipe-grid">
      <?php foreach ($recipes as $recipe): ?>
        <a href="recipeDetails.php?id=<?= $recipe['recipe_id'] ?>&source=all_recipes" class="card-link">
          <div class="card">
            <h3><?= htmlspecialchars($recipe['name']) ?></h3>
            <p><strong>Difficulty:</strong> <?= htmlspecialchars($recipe['difficulty']) ?></p>
            <p><strong>Creator:</strong> <?= htmlspecialchars($recipe['creator']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center;">No recipes found.</p>
  <?php endif; ?>

  <a href="index.php" class="back-button">← Back to Main Page</a>
</body>
</html>


