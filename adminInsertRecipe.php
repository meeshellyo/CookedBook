<?php
session_start();
require_once 'databaseCooked.php';
$db = Database::dbConnect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // collect the form data
    $name = $_POST['name'] ?? '';
    $difficulty = $_POST['difficulty'] ?? null;
    $description = $_POST['description'] ?? '';
    $instructions = $_POST['instructions'] ?? '';
    $admin_id = $_SESSION['admin_id'] ?? null;
    $ingredients = $_POST['ingredients'] ?? []; // array of ingredient IDs

    try {
        // insert recipe into the recipes table
        $stmt = $db->prepare("INSERT INTO recipes (admin_id, name, difficulty, description, instructions)
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_id, $name, $difficulty, $description, $instructions]);
        $recipe_id = $db->lastInsertId(); // get the new recipe ID

        // insert selected ingredients into recipe_ingredients table
        if (!empty($ingredients)) {
            $ingStmt = $db->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id) VALUES (?, ?)");
            foreach ($ingredients as $ingredient_id) {
                $ingStmt->execute([$recipe_id, $ingredient_id]);
            }
        }

        //needed for popup message
        $_SESSION['recipe_created'] = true;

        // redirect on success
        header("Location: adminManageRecipe.php");
        exit();

    } catch (PDOException $e) {
        echo "Error adding recipe: " . $e->getMessage();
    }
}
?>
