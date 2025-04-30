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
$user_id = $_SESSION['user_id'];

// grabs the user's favorited recipes along with all of its information.  order by most recently favorited
$stmt = $conn->prepare("
    SELECT r.recipe_id, r.name, r.difficulty, a.username AS creator
    FROM users_favorites uf
    JOIN recipes r ON uf.recipe_id = r.recipe_id
    JOIN admins a ON r.admin_id = a.admin_id
    WHERE uf.user_id = ?
    ORDER BY uf.favorited_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>My Favorites</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css" />
  <style>
    .recipe-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 2em;
      margin-top: 2em;
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
      font-size: 0.95rem;
      margin: 0.4em 0;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
    }

    .back-button {
      display: inline-block;
      margin: 3em auto 0;
      background-color: #ffeedd;
      color: #5a4b3c;
      padding: 0.6em 1.2em;
      border-radius: 12px;
      border: 1px solid #ffccb3;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
    }
  </style>
</head>
<body>
  <h2>My Favorited Recipes</h2>

  <?php if (!empty($favorites)): ?>
    <div class="recipe-grid">
      <?php foreach ($favorites as $fav): ?>
        <!-- redirects user to the recipe if they select their favorited recipe -->
        <a href="recipeDetails.php?id=<?= $fav['recipe_id'] ?>&source=myFavorites" class="card-link">
          <div class="card">
            <h3><?= htmlspecialchars($fav['name']) ?></h3>
            <p><strong>Difficulty:</strong> <?= htmlspecialchars($fav['difficulty']) ?></p>
            <p><strong>Creator:</strong> <?= htmlspecialchars($fav['creator']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center;">You haven't favorited any recipes yet.</p>
  <?php endif; ?>

  <div style="text-align: center;">
    <a href="index.php" class="back-button">← Back to Main Page</a>
  </div>
</body>
</html>
