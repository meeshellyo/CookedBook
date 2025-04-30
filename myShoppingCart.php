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
$userId = $_SESSION['user_id'];

// get user's shopping cart items
$stmt = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Shopping Cart</title>
  <link rel="stylesheet" href="css/styleUSERVIEW.css">
  <style>
    .cart-container {
      max-width: 700px;
      margin: 2em auto;
      background: #fff8ee;
      padding: 2em;
      border-radius: 10px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }

    .cart-container h2 {
      color: #5a4b3c;
      margin-bottom: 1em;
    }

    .cart-item {
      margin-bottom: 1em;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 0.5em;
      border-bottom: 1px solid #ddd0c7;
      font-size: 1rem;
      color: #4a3f35;
    }

    .remove-button {
      background: #ffdddd;
      border: 1px solid #ff9999;
      padding: 0.4em 0.8em;
      border-radius: 6px;
      text-decoration: none;
      color: #aa0000;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .remove-button:hover {
      background: #ffbbbb;
    }

    .back-button {
      display: inline-block;
      margin-top: 2em;
      background-color: #ffeedd;
      color: #5a4b3c;
      border: 1px solid #ffccb3;
      padding: 0.6em 1.2em;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .back-button:hover {
      background-color: #ffd9b3;
      color: #4a2c1a;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="cart-container">
    <h2>My Shopping Cart</h2>

    <?php if (empty($cartItems)): ?>
      <p>You have no items in your shopping cart yet.</p>
    <?php else: ?>
      <?php foreach ($cartItems as $item): ?>
        <div class="cart-item">
          <span><?= htmlspecialchars($item['ingredient_name']) ?><?= $item['quantity'] ? " (" . htmlspecialchars($item['quantity']) . ")" : '' ?></span>
          <a href="removeFromCart.php?id=<?= $item['cart_id'] ?>" class="remove-button">Remove</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <a href="index.php" class="back-button">← Back to Main Page</a>
  </div>
</body>
</html>
