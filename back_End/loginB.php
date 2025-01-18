<?php
global $conn;
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Kontrollo nëse përdoruesi është bllokuar
    $stmt = $conn->prepare("SELECT * FROM failed_logins WHERE email = ? AND lockout_time > NOW()");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Your account is locked. Please try again later.";
        log_login_attempt($conn, $email, $ip_address, 'locked');
        exit();
    }

    // Kontrollo nëse përdoruesi ekziston në tabelën `users`
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifiko fjalëkalimin
        if (password_verify($password, $user['password'])) {
            // Nëse logimi është i suksesshëm, reset tentativat
            $stmt = $conn->prepare("DELETE FROM failed_logins WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Krijo sesion
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Ruaj logun e suksesit
            log_login_attempt($conn, $email, $ip_address, 'success');

            // Ridrejto sipas rolit
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            // Regjistro tentativën e gabuar
            log_failed_attempt($conn, $email, $ip_address);
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
        log_login_attempt($conn, $email, $ip_address, 'failed');
    }

    $stmt->close();
    $conn->close();
}

function log_failed_attempt($conn, $email, $ip_address) {
    // Kontrollo nëse përdoruesi ka tentativa ekzistuese
    $stmt = $conn->prepare("SELECT * FROM failed_logins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
        $failed_attempts = $record['failed_attempts'] + 1;

        if ($failed_attempts >= 7) {
            // Blloko përdoruesin për 30 minuta
            $lockout_time = date("Y-m-d H:i:s", strtotime("+30 minutes"));
            $stmt = $conn->prepare("UPDATE failed_logins SET failed_attempts = ?, lockout_time = ? WHERE email = ?");
            $stmt->bind_param("iss", $failed_attempts, $lockout_time, $email);
        } else {
            // Përditëso tentativat e dështuara
            $stmt = $conn->prepare("UPDATE failed_logins SET failed_attempts = ? WHERE email = ?");
            $stmt->bind_param("is", $failed_attempts, $email);
        }
        $stmt->execute();
    } else {
        // Krijo rekord të ri për tentativën e parë
        $stmt = $conn->prepare("INSERT INTO failed_logins (email, attempted_at, ip_address, failed_attempts) VALUES (?, NOW(), ?, 1)");
        $stmt->bind_param("ss", $email, $ip_address);
        $stmt->execute();
    }
    $stmt->close();

    // Ruaj logun
    log_login_attempt($conn, $email, $ip_address, 'failed');
}

function log_login_attempt($conn, $email, $ip_address, $status) {
    $stmt = $conn->prepare("INSERT INTO login_logs (email, ip_address, status, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $email, $ip_address, $status);
    $stmt->execute();
    $stmt->close();
}

// Kontrollo sesionin për timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
