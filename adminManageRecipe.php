<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

$deletedPopup = false;
$createdPopup = false;
$updatedPopup = false;

if (isset($_SESSION['recipe_deleted']) && $_SESSION['recipe_deleted']) {
    $deletedPopup = true;
    unset($_SESSION['recipe_deleted']);
}
if (isset($_SESSION['recipe_created']) && $_SESSION['recipe_created']) {
    $createdPopup = true;
    unset($_SESSION['recipe_created']);
}
if (isset($_SESSION['recipe_updated']) && $_SESSION['recipe_updated']) {
    $updatedPopup = true;
    unset($_SESSION['recipe_updated']);
}

// this will get the action from query string EDIT, DELETE, or NULL
$action = $_GET['action'] ?? null;

try {
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
      <th>Difficulty (1-5)</th>
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

  <div style="margin-top: 2em;">
    <a href="landingAdminPage.php" class="back-button">&larr; Back to Dashboard</a>
  </div>

  <?php if ($deletedPopup): ?>
    <script>alert("Recipe successfully deleted.");</script>
  <?php endif; ?>
  <?php if ($createdPopup): ?>
    <script>alert("Recipe successfully added.");</script>
  <?php endif; ?>
  <?php if ($updatedPopup): ?>
    <script>alert("Recipe successfully updated.");</script>
  <?php endif; ?>

</body>
</html>



