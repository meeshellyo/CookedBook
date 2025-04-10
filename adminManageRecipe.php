<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

// this will get the action from query string EDIT,DELETE,NULL
$action = $_GET['action'] ?? null;

try {
    // join admins to get the creator for each recipe
    $stmt = $db->query("
        SELECT r.*, a.username AS admin_username
        FROM recipes r
        JOIN admins a ON r.admin_id = a.admin_id
        ORDER BY r.recipe_id DESC
    ");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching recipes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Recipes</title>
  <link rel="stylesheet" href="css/styleADMINCRUD.css" />
</head>
<body>
  <h2>All Recipes</h2>

  <table border="1" cellpadding="10">
    <tr>
      <th>Name</th>
      <th>Difficulty</th>
      <th>Description</th>
      <th>Instructions</th>
      <th>Created By</th>
      <th>Actions</th>
    </tr>

    <?php foreach ($recipes as $recipe): ?>
    <tr>
      <td><?= htmlspecialchars($recipe['name']) ?></td>
      <td><?= htmlspecialchars($recipe['difficulty']) ?></td>
      <td><?= nl2br(htmlspecialchars($recipe['description'])) ?></td>
      <td><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></td>
      <td><?= htmlspecialchars($recipe['admin_username']) ?></td>
      <td>
        <?php if ($action === 'edit'): ?>
          <a href="adminEditRecipe.php?id=<?= $recipe['recipe_id'] ?>">Edit</a>
        <?php elseif ($action === 'delete'): ?>
          <a href="adminDeleteRecipe.php?id=<?= $recipe['recipe_id'] ?>" onclick="return confirm('Delete this recipe?')">Delete</a>
        <?php else: ?>
          <a href="adminEditRecipe.php?id=<?= $recipe['recipe_id'] ?>">Edit</a> |
          <a href="adminDeleteRecipe.php?id=<?= $recipe['recipe_id'] ?>" onclick="return confirm('Delete this recipe?')">Delete</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>

    <!--this is the backspace button-->
  <div style="margin-top: 2em;">
    <a href="landingAdminPage.php" class="back-button">← Back to Dashboard</a>
  </div>
</body>
</html>


