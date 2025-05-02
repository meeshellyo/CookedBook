<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$conn = Database::dbConnect();
$userId = $_SESSION['user_id'];

$cartId = $_GET['id'] ?? null;
$ingredient = $_POST['ingredient_name'] ?? null;

// remove by cart_id (used in myShoppingCart.php)
if ($cartId) {
  $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE cart_id = ? AND user_id = ?");
  $stmt->execute([$cartId, $userId]);
}

// remove by ingredient_name (used in recipe page)
if ($ingredient) {
  $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = ? AND ingredient_name = ?");
  $stmt->execute([$userId, $ingredient]);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
