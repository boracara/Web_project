<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo nëse përdoruesi është administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../front_End/login.html");
    exit();
}

// Analiza e profesioneve
$profession_query = "SELECT profession, COUNT(*) AS total FROM user_profiles GROUP BY profession ORDER BY total DESC";
$profession_result = $conn->query($profession_query);

// Analiza e moshës
$age_query = "SELECT FLOOR(DATEDIFF(CURRENT_DATE, birthdate) / 365) AS age, COUNT(*) AS total FROM users GROUP BY age ORDER BY age";
$age_result = $conn->query($age_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analiza e Përdoruesve</title>
    <link rel="stylesheet" href="../front_End/user.css">
</head>
<body>
<div class="analysis-container">
    <h1>Analiza e Përdoruesve</h1>

    <h2>Profesione të Regjistruara</h2>
    <table border="1">
        <tr>
            <th>Profesioni</th>
            <th>Numri i Përdoruesve</th>
        </tr>
        <?php while ($row = $profession_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['profession']) ?></td>
                <td><?= $row['total'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Shpërndarja e Moshave</h2>
    <table border="1">
        <tr>
            <th>Mosha</th>
            <th>Numri i Përdoruesve</th>
        </tr>
        <?php while ($row = $age_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['age'] ?> vjeç</td>
                <td><?= $row['total'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
