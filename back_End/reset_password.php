<?php
global $conn;
include('config.php');

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Validate inputs
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        echo "All fields are required.";
        exit();
    }

    if ($new_password !== $confirm_password) {
        echo "Passwords do not match. Please try again.";
        exit();
    }

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Email not found. Please try again.";
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ?, remember_token = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        echo "Password has been reset successfully.";
        // Redirect to login page
        header("Location: ../front_End/login.html");
        exit();
    } else {
        echo "An error occurred. Please try again.";
    }
}
?>
