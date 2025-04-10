<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

// baibai if u not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// this gets the ingredients 
$stmt = $db->query("SELECT * FROM ingredients ORDER BY name ASC");
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Recipe</title>
    <link rel="stylesheet" href="css/styleADMINCRUD.css" />
    <style>
    .ingredient-fieldset {
      border: 1px solid #ccc;
      padding: 1em;
    }

    .ingredient-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 0.5em 1.5em;
    }

    .ingredient-item {
      font-weight: bold;
      font-family: 'Courier New', monospace;
      display: flex;
      align-items: center;
      gap: 0.5em;
    }
</style>

</head>
<body>
    <h2>Create a New Recipe</h2>
    <!-- Step 1: Create recipe -->
    <form method="POST" action="adminInsertRecipe.php">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Difficulty (1–5):</label><br>
        <input type="number" name="difficulty" min="1" max="5"><br><br>

        <label>Description:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Instructions:</label><br>
        <textarea name="instructions" required></textarea><br><br>

        <fieldset class="ingredient-fieldset">
            <legend>Select Ingredients:</legend>
            <div class="ingredient-grid">
                <?php foreach ($ingredients as $ingredient): ?>
                    <label class="ingredient-item">
                        <input type="checkbox" name="ingredients[]" value="<?= $ingredient['ingredient_id'] ?>">
                        <?= htmlspecialchars($ingredient['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset><br>

        <input type="submit" value="Add Recipe">
    </form>

    <!-- this is back button -->
    <div style="margin-top: 2em;">
        <a href="landingAdminPage.php" style="
            display: inline-block;
            background-color: #ffeedd;
            color: #5a4b3c;
            padding: 0.6em 1.2em;
            border-radius: 12px;
            border: 1px solid #ffccb3;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;">
            &larr; Back to Dashboard
        </a>
    </div>
</body>
</html>
