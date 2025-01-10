<?php
// Përfshi skedarin e konfigurimit
global $conn;
include('config.php');

// Kontrollo nëse të dhënat janë dërguar nga forma
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Merr të dhënat nga forma
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $admin_code = $_POST['admin_code'] ?? ''; // Opsional

    // Kontrollo vlefshmërinë e email-it
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Emaili është i pavlefshëm.");
    }

    // Kontrollo fjalëkalimin për siguri
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        die("Fjalëkalimi nuk plotëson kërkesat e sigurisë.");
    }

    // Kontrollo rolin bazuar në email ose admin_code
    $role = (strpos($email, '@companydomain.com') !== false) ? 'admin' : 'user';
    $valid_admin_code = "ADMIN123";
    if ($admin_code === $valid_admin_code) {
        $role = 'admin';
    }

    // Kontrollo nëse email-i ekziston tashmë
    $check_email_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Ky email ekziston tashmë!");
    }

    // Enkripto fjalëkalimin
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Fut të dhënat në databazë
    $insert_query = "INSERT INTO users (first_name, last_name, email, birthdate, gender, password, role) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssssss", $first_name, $last_name, $email, $birthdate, $gender, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Përdoruesi u regjistrua me sukses!";
        // Opsionale: Ridrejto te faqja e login-it
        header("Location: /front_End/login.html");
        exit();
    } else {
        echo "Gabim gjatë regjistrimit: " . $stmt->error;
    }

    // Mbyll burimet
    $stmt->close();
    $conn->close();
} else {
    // Nëse forma nuk është dërguar saktë, ridrejtoje te faqja e regjistrimit
    header("Location: /front_End/register.html");
    exit();
}
?>
