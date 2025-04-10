<?php
session_start();
require_once("databaseCooked.php");

$conn = Database::dbConnect();

$chefInfo = [
  'Michelle' => [
    'photo' => 'photos/chefmichelle.png',
    'bio' => 'Hi!  I am Michelle and I blehblebhksdfjsdf'
  ],
  'Campbell' => [
    'photo' => 'photos/chefcamp.png',
    'bio' => 'wejgaskjdfawkjegahsdfajwegasdf'
  ],
    'Joshua' => [
    'photo' => 'photos/chefjosh.png',
    'bio' => 'asdjkghalwef'
  ],
  'Tyler' => [
    'photo' => 'photos/cheftt.png',
    'bio' => 'ajsdkhwkljfksldgwa'
  ]
];

$creator = $_GET['creator'] ?? null;

if (!$creator) {
    die("No creator specified.");
}

// get admin_id from username
$stmt = $conn->prepare("SELECT admin_id, username FROM admins WHERE LOWER(username) = LOWER(?)");
$stmt->execute([$creator]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Chef not found.");
}

$admin_id = $admin['admin_id'];

// get all recipes by this admin
$stmt = $conn->prepare("
    SELECT recipe_id, name, difficulty
    FROM recipes
    WHERE admin_id = ?
    ORDER BY recipe_id DESC
");
$stmt->execute([$admin_id]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($admin['username']) ?>'s Recipes</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css">
  <style>
    .recipe-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 2em;
      padding-top: 2em;
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
    }
    .chef-profile {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2em;
      margin: 2em auto;
      max-width: 900px;
      background-color: #fff8ee;
      padding: 2em;
      border-radius: 20px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
    }

    .chef-photo {
      width: 200px;
      height: auto;
      border-radius: 12px;
      object-fit: cover;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      flex-shrink: 0;
    }

    .chef-bio {
      display: flex;
      flex-direction: column;
      justify-content: center;
      max-width: 500px;
      text-align: left;
    }

    .chef-bio h2 {
      font-size: 1.6rem;
      margin-bottom: 0.5em;
      color: #5a4b3c;
    }

    .chef-bio p {
      font-size: 1.1rem;
      color: #5a4b3c;
      line-height: 1.6;
    }
  </style>
</head>
<body>
  <h2><?= htmlspecialchars($admin['username']) ?>'s Recipes</h2>
<?php if (isset($chefInfo[$admin['username']])): ?>
  <div class="chef-profile">
    <img class="chef-photo" src="<?= $chefInfo[$admin['username']]['photo'] ?>" alt="<?= $admin['username'] ?>'s Photo">
    <div class="chef-bio">
      <h2>Chef <?= htmlspecialchars($admin['username']) ?>'s Bio</h2>
      <p><?= $chefInfo[$admin['username']]['bio'] ?></p>
    </div>
  </div>
<?php endif; ?>
  <?php if (!empty($recipes)): ?>
    <div class="recipe-grid">
      <?php foreach ($recipes as $recipe): ?>
<a href="recipeDetails.php?id=<?= $recipe['recipe_id'] ?>&creator=<?= urlencode($admin['username']) ?>&source=userRecipes" class="card-link">
            <div class="card">
            <h3><?= htmlspecialchars($recipe['name']) ?></h3>
            <p><strong>Difficulty:</strong> <?= htmlspecialchars($recipe['difficulty']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center;">No recipes found for this chef.</p>
  <?php endif; ?>

  <a href="index.php" class="back-button">← Back to Main Page</a>
</body>
</html>
