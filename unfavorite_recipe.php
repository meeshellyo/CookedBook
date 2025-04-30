<?php
session_start();
require_once("databaseCooked.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = Database::dbConnect();

if (isset($_POST['recipe_id'])) {
    $recipeId = $_POST['recipe_id'];
    $userId = $_SESSION['user_id'];

    // delete from users favorites
    $stmt = $conn->prepare("DELETE FROM users_favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$userId, $recipeId]);
}

// redirects
$source = $_POST['source'] ?? 'all_recipes';
$creator = $_POST['creator'] ?? '';

$redirectUrl = "recipeDetails.php?id=$recipeId&source=" . urlencode($source);
if (!empty($creator)) {
    $redirectUrl .= "&creator=" . urlencode($creator);
}

header("Location: $redirectUrl");
exit();
?>
