<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo nëse përdoruesi është administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../front_End/login.html");
    exit();
}

$user_id = $_GET['id'];

// Fshi përdoruesin nga baza e të dhënave
$query = "DELETE FROM users WHERE id = '$user_id'";
if ($conn->query($query)) {
    header("Location: ../back_End/admin_dashboard.php");
    exit();
} else {
    echo "Gabim gjatë fshirjes së përdoruesit.";
}
?>
