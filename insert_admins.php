<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Start of script<br>";

require_once 'databaseCooked.php';
$conn = Database::dbConnect();

echo "Connected to database<br>";

$admins = [
    ['username' => 'Michelle', 'password' => 'meeshellpass'],
    ['username' => 'Campbell', 'password' => 'campbellpass'],
    ['username' => 'Joshua', 'password' => 'joshpass'],
    ['username' => 'Tyler', 'password' => 'ttpass']
];

foreach ($admins as $admin) {
    $username = $admin['username'];
    $hashedPassword = password_hash($admin['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");

    if (!$stmt) {
        die("❌ Prepare failed");
    }

    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', $hashedPassword);

    if ($stmt->execute()) {
        echo "inserted admin: $username<br>";
    } else {
        echo "failed to insert $username<br>";
    }
}
?>
