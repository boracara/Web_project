<?php
$servername = "localhost";
$username = "root";
$password = ""; // Lëre bosh nëse nuk ke fjalëkalim për MySQL
$dbname = "platforma_db"; // Emri i saktë i bazës së të dhënave

// Krijo lidhjen
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Kontrollo lidhjen
if ($conn->connect_error) {
    die("Lidhja me databazën dështoi: " . $conn->connect_error);
}

try {
    // Krijo një lidhje PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Shfaq gabimin në rast se lidhja dështon
    die("Lidhja me databazën dështoi: " . $e->getMessage());
}
?>
