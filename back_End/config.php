<?php
$servername = "localhost";
$username = "root";
$password = ""; // Bosh nëse nuk ke vendosur fjalëkalim në MySQL
$dbname = "platforma_db"; // Kontrollo që ky është emri i saktë i bazës së të dhënave

$conn = new mysqli($servername, $username, $password, $dbname ); // 3306 është porti standard


// Kontrollo lidhjen
if ($conn->connect_error) {
    die("Lidhja me bazën e të dhënave dështoi: " . $conn->connect_error);
}
?>
