<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo nëse përdoruesi është administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../front_End/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Kriptimi i fjalëkalimit
    $role = $conn->real_escape_string($_POST['role']);

    $query = "INSERT INTO users (first_name, last_name, email, password, role) VALUES ('$first_name', '$last_name', '$email', '$password', '$role')";
    if ($conn->query($query)) {
        echo "Përdoruesi u shtua me sukses.";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Gabim gjatë shtimit të përdoruesit.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Web_project/front_End/adduser.css">
    <title>Shto Përdorues</title>
</head>
<body>
<h1>Shto Përdorues të Ri</h1>
<form method="post">
    <label for="first_name">Emri:</label><br>
    <input type="text" id="first_name" name="first_name" required><br><br>

    <label for="last_name">Mbiemri:</label><br>
    <input type="text" id="last_name" name="last_name" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Fjalëkalimi:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <label for="role">Roli:</label><br>
    <select id="role" name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br><br>


    <button type="submit">Shto</button>
</form>
</body>
</html>
