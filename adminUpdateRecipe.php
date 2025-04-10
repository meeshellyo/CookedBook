<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $difficulty = $_POST['difficulty'] ?? null;
    $description = $_POST['description'] ?? '';
    $instructions = $_POST['instructions'] ?? '';
    $ingredients = $_POST['ingredients'] ?? [];

    if (!$id) {
        die("Missing recipe ID.");
    }

    try {
        // Update recipe
        $stmt = $db->prepare("UPDATE recipes SET name = ?, difficulty = ?, description = ?, instructions = ? WHERE recipe_id = ?");
        $stmt->execute([$name, $difficulty, $description, $instructions, $id]);

        // Delete old ingredients
        $db->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?")->execute([$id]);

        // Insert new ingredients
        if (!empty($ingredients)) {
            $stmt = $db->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id) VALUES (?, ?)");
            foreach ($ingredients as $ingId) {
                $stmt->execute([$id, $ingId]);
            }
        }

        header("Location: adminManageRecipe.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
