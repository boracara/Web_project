<?php
global $conn;
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_End/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM donations WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donations</title>
    <link rel="stylesheet" href="../front_End/style.css">
</head>
<body>
<h1>My Donations</h1>
<table>
    <tr>
        <th>Transaction ID</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['transaction_id']) ?></td>
            <td>$<?= htmlspecialchars($row['amount']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
