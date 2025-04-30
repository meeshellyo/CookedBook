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

    //adds into userfavorite
    $stmt = $conn->prepare("INSERT IGNORE INTO users_favorites (user_id, recipe_id) VALUES (?, ?)");
    $stmt->execute([$userId, $recipeId]);
}

//redirects
$source = $_POST['source'] ?? 'all_recipes';
$creator = $_POST['creator'] ?? '';

$redirectUrl = "recipeDetails.php?id=$recipeId&source=" . urlencode($source);
if (!empty($creator)) {
    $redirectUrl .= "&creator=" . urlencode($creator);
}

header("Location: $redirectUrl");
exit();
?>
