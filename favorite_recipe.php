<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['recipe_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$recipe_id = $_POST['recipe_id'];

$conn = Database::dbConnect();

$stmt = $conn->prepare("INSERT IGNORE INTO users_favorites (user_id, recipe_id) VALUES (?, ?)");
$stmt->execute([$user_id, $recipe_id]);

// Redirect back to recipe details
header("Location: recipeDetails.php?id=$recipe_id");
exit();
?>
