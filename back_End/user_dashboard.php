<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo nëse përdoruesi është loguar
if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_End/login.html");
    exit();
}

// Merr ID-në e përdoruesit të kërkuar ose atë nga sesioni
$requested_user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Kontrollo nëse përdoruesi ka të drejtë të aksesojë profilin
if ($_SESSION['role'] !== 'admin' && $requested_user_id != $_SESSION['user_id']) {
    die("Nuk keni leje për të aksesuar këtë profil.");
}

// Merr të dhënat e përdoruesit nga tabela 'users'
$query = "
    SELECT first_name, last_name, email, birthdate, gender, profile_picture 
    FROM users 
    WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $requested_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Të dhënat e përdoruesit nuk u gjetën.");
}

// Merr të dhënat nga tabela 'user_profiles'
$profile_query = "
    SELECT COALESCE(profession, '') AS profession, COALESCE(description, '') AS description 
    FROM user_profiles 
    WHERE user_id = ?";
$stmt = $conn->prepare($profile_query);
$stmt->bind_param("i", $requested_user_id);
$stmt->execute();
$user_profile = $stmt->get_result()->fetch_assoc();

// Nëse user_profile nuk ekziston, inicializo vlera bosh
if (!$user_profile) {
    $user_profile = ['profession' => '', 'description' => ''];
}

// Përpunimi i të dhënave të formularit për përditësimin e profilit
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
        $target_dir = "../uploads/";
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $profile_picture = $target_dir . uniqid("profile_", true) . "." . $file_extension;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    // Përditëso të dhënat në tabelën 'users'
    $update_user_query = "
        UPDATE users 
        SET first_name = ?, last_name = ?, email = ?, birthdate = ?, gender = ?, profile_picture = ? 
        WHERE id = ?";
    $stmt = $conn->prepare($update_user_query);
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $birthdate, $gender, $profile_picture, $requested_user_id);
    $stmt->execute();

    // Përditëso ose shto të dhënat në tabelën 'user_profiles'
    $profile_query = "SELECT * FROM user_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($profile_query);
    $stmt->bind_param("i", $requested_user_id);
    $stmt->execute();
    $profile_result = $stmt->get_result();

    if ($profile_result->num_rows > 0) {
        $update_profile_query = "
            UPDATE user_profiles 
            SET profession = ?, description = ? 
            WHERE user_id = ?";
        $stmt = $conn->prepare($update_profile_query);
        $stmt->bind_param("ssi", $profession, $description, $requested_user_id);
        $stmt->execute();
    } else {
        $insert_profile_query = "
            INSERT INTO user_profiles (user_id, profession, description) 
            VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_profile_query);
        $stmt->bind_param("iss", $requested_user_id, $profession, $description);
        $stmt->execute();
    }

    // Ridrejto te dashboard pas përditësimit
    header("Location: ../back_End/user_dashboard.php?id=" . $requested_user_id);
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
            <label for="first_name">First Name:</label><br>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required><br><br>

            <label for="last_name">Last Name:</label><br>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <label for="birthdate">Birthdate:</label><br>
            <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>" required><br><br>

            <label for="gender">Gender:</label><br>
            <select id="gender" name="gender" required>
                <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                <option value="rather_not_say" <?= $user['gender'] == 'rather_not_say' ? 'selected' : '' ?>>Rather not say</option>
            </select><br><br>


            <label for="profession">Employment Field:</label><br>
            <select id="profession" name="profession" required>
                <option value="Tregti" <?= $user_profile['profession'] == 'Tregti' ? 'selected' : '' ?>>Trade</option>
                <option value="Shërbim Klienti" <?= $user_profile['profession'] == 'Shërbim Klienti' ? 'selected' : '' ?>>Customer Service</option>
                <option value="Biznes" <?= $user_profile['profession'] == 'Biznes' ? 'selected' : '' ?>>Business</option>
                <option value="Student" <?= $user_profile['profession'] == 'Student' ? 'selected' : '' ?>>Student</option>
                <option value="IT dhe Teknologji" <?= $user_profile['profession'] == 'IT dhe Teknologji' ? 'selected' : '' ?>>IT and Technology</option>
                <option value="Mjekësi" <?= $user_profile['profession'] == 'Mjekësi' ? 'selected' : '' ?>>Medicine</option>
                <option value="Arsim" <?= $user_profile['profession'] == 'Arsim' ? 'selected' : '' ?>>Education</option>
                <option value="Transport" <?= $user_profile['profession'] == 'Transport' ? 'selected' : '' ?>>Transport</option>
                <option value="Inxhinieri" <?= $user_profile['profession'] == 'Inxhinieri' ? 'selected' : '' ?>>Engineering</option>
                <option value="Tjetër" <?= $user_profile['profession'] == 'Tjetër' ? 'selected' : '' ?>>Other</option>
            </select><br><br>

            <label for="description">Personal Description:</label><br>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($user_profile['description']) ?></textarea><br><br>

            <label for="profile_picture">Profile Picture:</label><br>
            <input type="file" id="profile_picture" name="profile_picture"><br><br>

            <div class="user-actions">
                <a href="../front_End/sendmessage.html?receiver_email=<?= urlencode($user['email']); ?>" class="button btn-primary">Dërgo Mesazh</a>
                <a href="../back_End/messages.php?receiver_email=<?= urlencode($_SESSION['email']); ?>" class="button btn-secondary">Shiko Mesazhet e Mia</a>
                <a href="../back_End/announcements.php" class="button btn-info">Shiko Njoftimet nga ADMIN</a>
                <a href="../back_End/events.php" class="button btn-success">Shiko Eventet</a>
            </div>


            <button type="submit">Përditëso</button>
            <a href="../back_End/logout.php" class="logout-button">Log Out</a>

            <a href="../front_End/entrance.html" class="button">Kthehu te Faqja Kryesore</a>

        </form>
    </div>
</div>
</body>
</html>
