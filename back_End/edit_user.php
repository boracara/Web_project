<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo nëse përdoruesi është administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../front_End/login.html");
    exit();
}

// Merr ID-në e përdoruesit që do modifikohet
$user_id = $_GET['id'];

// Merr të dhënat e përdoruesit nga baza e të dhënave
$query = "SELECT first_name, last_name, email, birthdate, gender, profile_picture FROM users WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Përdoruesi nuk u gjet.");
}

// Përpunimi i formularit për përditësimin e të dhënave
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $profile_picture = $user['profile_picture'];

    // Kontrollo nëse është ngarkuar një foto e re
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $profile_picture = $target_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    // Përditëso të dhënat në bazën e të dhënave
    $update_query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email', birthdate = '$birthdate', gender = '$gender', profile_picture = '$profile_picture' WHERE id = '$user_id'";
    if ($conn->query($update_query)) {
        echo "Të dhënat u përditësuan me sukses.";
        header("Location: ../back_End/admin_dashboard.php");
        exit();
    } else {
        echo "Gabim gjatë përditësimit të të dhënave.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifiko Përdorues</title>
    <link rel="stylesheet" href="../front_End/adduser.css">
</head>
<body>
<div class="profile-container">
    <h1>Modifiko Përdorues</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="first_name">Emri:</label><br>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required><br><br>

        <label for="last_name">Mbiemri:</label><br>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label for="birth_date">Data e Lindjes:</label><br>
        <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>" required><br><br>

        <label for="gender">Gjinia:</label><br>
        <select id="gender" name="gender">
            <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Mashkull</option>
            <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Femër</option>
            <option value="rather_not_say" <?= $user['gender'] == 'rather_not_say' ? 'selected' : '' ?>>Rather not say</option>
        </select><br><br>

        <label for="profile_picture">Foto e Profilit:</label><br>
        <input type="file" id="profile_picture" name="profile_picture"><br><br>
        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" width="100" alt="Foto e Profilit"><br><br>

        <button type="submit">Përditëso</button>
    </form>
</div>
</body>
</html>
