<?php
$servername = "localhost";
$username = "root";
$password = ""; // Lëre bosh nëse nuk ke fjalëkalim për MySQL
$dbname = "platforma_db"; // Emri i saktë i bazës së të dhënave

// Krijo lidhjen
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrollo lidhjen
if ($conn->connect_error) {
    die("Lidhja me databazën dështoi: " . $conn->connect_error);
}
?>
