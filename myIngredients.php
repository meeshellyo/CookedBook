<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$conn = Database::dbConnect();

// grabs the ingredient_id and name
$stmt = $conn->query("SELECT ingredient_id, name FROM ingredients ORDER BY name ASC");
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>My Ingredients</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css">
  <style>
    .ingredient-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* 4 equal columns */
      gap: 1em 2em; /* vertical and horizontal spacing */
      max-width: 1000px;
      margin: 2em auto;
      padding: 1em;
    }

    .ingredient-item {
      display: flex;
      align-items: center;
      gap: 0.5em;
      font-size: 1rem;
      color: #5a4b3c;
    }

    .ingredient-item input[type="checkbox"] {
      transform: scale(1.2);
      cursor: pointer;
    }

    .action-link {
      background-color: #ffeedd;
      color: #5a4b3c;
      padding: 0.6em 1.5em;
      border-radius: 8px;
      border: 1px solid #ffccb3;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
      font-size: 1rem;
    }

    .action-link:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
    }

    .back-button {
      display: inline-block;
      margin-top: 2em;
      background-color: #ffeedd;
      color: #5a4b3c;
      padding: 0.6em 1.2em;
      border-radius: 8px;
      border: 1px solid #ffccb3;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
    }

    h2 {
      text-align: center;
      margin-top: 2em;
      font-size: 2rem;
      color: #5a4b3c;
    }
  </style>
</head>
<body>

  <h2>Select Your Ingredients</h2>

  <form action="ingredientResults.php" method="POST">
    <div class="ingredient-grid">
      <?php foreach ($ingredients as $ingredient): ?>
        <label class="ingredient-item">
          <input type="checkbox" name="ingredients[]" value="<?= $ingredient['ingredient_id'] ?>">
          <?= htmlspecialchars($ingredient['name']) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 2em;">
      <input type="submit" value="Find Recipes" class="action-link">
    </div>
  </form>

  <div style="text-align: center;">
    <a href="index.php" class="back-button">← Back to Main Page</a>
  </div>

</body>
</html>
