<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$conn = Database::dbConnect();
$cartId = $_GET['id'] ?? null;

if ($cartId) {
  $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE cart_id = ? AND user_id = ?");
  $stmt->execute([$cartId, $_SESSION['user_id']]);
}

header("Location: myShoppingCart.php");
exit();
