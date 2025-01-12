<?php
global $conn;
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $admin_code = $_POST['admin_code'] ?? '';

    // Kontrollo vlefshmërinë e email-it
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Emaili është i pavlefshëm.");
    }

    // Kontrollo fjalëkalimin për siguri
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        die("Fjalëkalimi nuk plotëson kërkesat e sigurisë.");
    }

    $role = (strpos($email, '@companydomain.com') !== false) ? 'admin' : 'user';
    $valid_admin_code = "ADMIN123";
    if ($admin_code === $valid_admin_code) {
        $role = 'admin';
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Ky email ekziston tashmë!");
    }

    $verification_code = rand(100000, 999999); // Gjenero kodin e verifikimit
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $insert_query = "INSERT INTO users (first_name, last_name, email, birthdate, gender, password, role, verification_code) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $birthdate, $gender, $hashed_password, $role, $verification_code);

    if ($stmt->execute()) {
        // Dërgo kodin e verifikimit në email
        $to = $email;
        $subject = "Email Verification Code";
        $message = "Hello $first_name,\n\nYour verification code is: $verification_code\n\nPlease enter this code to verify your email address.";
        $headers = "From: no-reply@webplatform.com";

        if (mail($to, $subject, $message, $headers)) {
            // Shfaq një alert dhe ridrejto përdoruesin te faqja e verifikimit
            echo "<script>
                alert('Registration successful! A verification code has been sent to your email.');
                window.location.href = '/Web_project/front_End/verify.html';
                </script>";
        } else {
            echo "Failed to send verification email.";
        }
    } else {
        echo "Gabim gjatë regjistrimit: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: /front_End/register.html");
    exit();
}
?>
