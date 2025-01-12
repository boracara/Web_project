<?php
global $conn;
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Përgatit query për të marrë të dhënat e përdoruesit
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_verified = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Lejo logimin
    } else {
        echo "Your email is not verified. Please check your inbox.";
    }

    // Kontrollo nëse përdoruesi ekziston
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Inicializo variablën $user me të dhënat e përdoruesit

        // Kontrollo fjalëkalimin
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Ridrejto sipas rolit
            if ($user['role'] === 'admin') {
                header("Location: /Web_project/back_End/admin_dashboard.php"); // Ridrejtim për admin
            } else {
                header("Location: /Web_project/back_End/user_dashboard.php"); // Ridrejtim për user
            }
            exit();

        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
