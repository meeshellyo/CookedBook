<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to unfavorite a recipe.");
}

$userId = $_SESSION['user_id'];
$recipeId = $_POST['recipe_id'] ?? null;
$source = $_POST['source'] ?? 'all_recipes'; // pulled from the form
$creator = $_POST['creator'] ?? null;

if (!$recipeId) {
    die("Invalid request.");
}

$conn = Database::dbConnect();

// Delete from favorites
$stmt = $conn->prepare("DELETE FROM users_favorites WHERE user_id = ? AND recipe_id = ?");
$stmt->execute([$userId, $recipeId]);

// Determine proper redirect based on source
if ($source === 'myFavorites') {
    $redirect = 'myFavorites.php';
} elseif ($source === 'userRecipes' && $creator) {
    $redirect = 'usersRecipes.php?creator=' . urlencode($creator);
} else {
    $redirect = 'all_recipes.php';
}

header("Location: $redirect");
exit();
