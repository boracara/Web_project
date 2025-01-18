<?php
global $conn;
session_start();
require 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../front_End/login.html");
    exit();
}

// Fetch all donations
$query = "SELECT d.id, u.first_name, u.last_name, d.payer_email, d.amount, d.status, d.created_at 
          FROM donations d 
          JOIN users u ON d.user_id = u.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Donations</title>
    <link rel="stylesheet" href="../front_End/style.css">
</head>
<body>
    <h1>Donation Logs</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['payer_email']) ?></td>
                <td>$<?= htmlspecialchars($row['amount']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
