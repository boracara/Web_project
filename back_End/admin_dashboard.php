<?php
global $conn;
session_start();
require 'config.php';



// Kontrollo nëse përdoruesi është administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../front_End/login.html");
    exit();
}

// Merr listën e të gjithë përdoruesve
$query = "SELECT id, first_name, last_name, email, role FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Web_project/front_End/admin.css">
    <title>Lista e Përdoruesve</title>
</head>
<body>
<h1>Lista e Përdoruesve</h1>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Emri</th>
        <th>Mbiemri</th>
        <th>Email</th>
        <th>Roli</th>
        <th>Veprime</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
                <a href="../back_End/user_dashboard.php?id=<?= $row['id'] ?>">Shiko</a> |
                <a href="../back_End/edit_user.php?id=<?= $row['id'] ?>">Modifiko</a> |
                <a href="../back_End/delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Jeni të sigurt?')">Fshij</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<a href="../back_End/add_user.php">Shto Përdorues</a>

<a href="../back_End/admin_donations.php">View All Donations</a>

<nav>
    <ul>
        <li><a href="../back_End/admin_analysis.php">Analiza e Përdoruesve</a></li>
    </ul>
</nav>

</body>
</html>
