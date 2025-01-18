<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo sesionin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_End/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$requested_user_id = $_GET['id'] ?? $user_id; // ID-ja e kërkuar nga URL-ja ose ID-ja e vetë përdoruesit

// Lejo administratorët të aksesojnë të gjitha profilet
if ($_SESSION['role'] !== 'admin' && $requested_user_id != $user_id) {
    die("Nuk keni leje për të aksesuar këtë profil.");
}

// Merr të dhënat e përdoruesit
$query = "SELECT first_name, last_name, email, birthdate, gender, profile_picture FROM users WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Të dhënat e përdoruesit nuk u gjetën.");
}

// Merr fushën e punësimit dhe përshkrimin personal nga tabela user_profiles
$profile_query = "SELECT profession, description FROM user_profiles WHERE user_id = '$user_id'";
$profile_result = $conn->query($profile_query);

if ($profile_result->num_rows > 0) {
    $user_profile = $profile_result->fetch_assoc();
} else {
    $user_profile = ['profession' => '', 'description' => ''];
}

// Përditësimi i profilit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $profession = $conn->real_escape_string($_POST['profession']);
    $description = $conn->real_escape_string($_POST['description']);
    $profile_picture = $user['profile_picture'];

    // Ngarkimi i fotos së profilit nëse ekziston një foto e re
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "../uploads/"; // Rruga drejt direktorisë së ngarkimit
        $profile_picture = $target_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }


    // Përditëso të dhënat në tabelën users
    $update_user_query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email', birthdate = '$birthdate', gender = '$gender', profile_picture = '$profile_picture' WHERE id = '$user_id'";
    $conn->query($update_user_query);

    // Përditëso të dhënat në tabelën user_profiles
    if ($profile_result->num_rows > 0) {
        $update_profile_query = "UPDATE user_profiles SET profession = '$profession', description = '$description' WHERE user_id = '$user_id'";
        $conn->query($update_profile_query);
    } else {
        $insert_profile_query = "INSERT INTO user_profiles (user_id, profession, description) VALUES ('$user_id', '$profession', '$description')";
        $conn->query($insert_profile_query);
    }


    // Rifresko faqen
    header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili i Përdoruesit</title>
    <link rel="stylesheet" href="../front_End/user.css">
</head>
<body>
<div class="profile-container">
    <div class="profile-picture">
        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Foto e Profilit">
    </div>
    <div class="profile-details">
        <form method="post" enctype="multipart/form-data">
            <label for="first_name">Emri:</label><br>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required><br><br>

            <label for="last_name">Mbiemri:</label><br>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <label for="birthdate">Data e Lindjes:</label><br>
            <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>" required><br><br>

            <label for="gender">Gjinia:</label><br>
            <select id="gender" name="gender">
                <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Mashkull</option>
                <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Femër</option>
                <option value="rather_not_say" <?= $user['gender'] == 'rather_not_say' ? 'selected' : '' ?>>Rather not say</option>
            </select><br><br>

            <label for="profession">Fusha e Punësimit:</label><br>
            <select id="profession" name="profession" required>
                <option value="Tregti" <?= $user_profile['profession'] == 'Tregti' ? 'selected' : '' ?>>Tregti</option>
                <option value="Shërbim Klienti" <?= $user_profile['profession'] == 'Shërbim Klienti' ? 'selected' : '' ?>>Shërbim Klienti</option>
                <option value="Biznes" <?= $user_profile['profession'] == 'Biznes' ? 'selected' : '' ?>>Biznes</option>
                <option value="Student" <?= $user_profile['profession'] == 'Student' ? 'selected' : '' ?>>Student</option>
                <option value="IT dhe Teknologji" <?= $user_profile['profession'] == 'IT dhe Teknologji' ? 'selected' : '' ?>>IT dhe Teknologji</option>
                <option value="Mjekësi" <?= $user_profile['profession'] == 'Mjekësi' ? 'selected' : '' ?>>Mjekësi</option>
                <option value="Arsim" <?= $user_profile['profession'] == 'Arsim' ? 'selected' : '' ?>>Arsim</option>
                <option value="Transport" <?= $user_profile['profession'] == 'Transport' ? 'selected' : '' ?>>Transport</option>
                <option value="Inxhinieri" <?= $user_profile['profession'] == 'Inxhinieri' ? 'selected' : '' ?>>Inxhinieri</option>
                <option value="Tjetër" <?= $user_profile['profession'] == 'Tjetër' ? 'selected' : '' ?>>Tjetër</option>
            </select><br><br>

            <label for="description">Përshkrimi Personal:</label><br>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($user_profile['description']) ?></textarea><br><br>

            <label for="profile_picture">Foto e Profilit:</label><br>
            <input type="file" id="profile_picture" name="profile_picture"><br><br>


            <a href="../front_End/donate.html" class="btn">Make a Donation</a>
            <a href="../back_End/user_donations.php">View My Donations</a>


            <button type="submit">Përditëso</button>
        </form>
    </div>
</div>
</body>
</html>
