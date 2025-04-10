<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

// get recipe ID from URL
$id = $_GET['id'] ?? null;

try {
    // deleteee the recipe
    $stmt = $db->prepare("DELETE FROM recipes WHERE recipe_id = ?");
    $stmt->execute([$id]);

    // redirect back to adminManageRecipe page
    header("Location: adminManageRecipe.php");
    exit();

    // error if doesnt succeed
} catch (PDOException $e) {
    echo "Error deleting recipe: " . $e->getMessage();
}
?>
