<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$ingredient = $_POST['ingredient_name'] ?? '';
$quantity = $_POST['quantity'] ?? '';

if ($ingredient) {
  $conn = Database::dbConnect();

  //stops ddoubles
  $check = $conn->prepare("SELECT * FROM shopping_cart WHERE user_id = ? AND ingredient_name = ?");
  $check->execute([$userId, $ingredient]);
  
  if (!$check->fetch()) {
    $stmt = $conn->prepare("INSERT INTO shopping_cart (user_id, ingredient_name, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $ingredient, $quantity]);
  }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
