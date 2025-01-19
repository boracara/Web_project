<?php
global $conn;
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Ju duhet të jeni i loguar për të votuar.");
}

$user_id = $_SESSION['user_id'];
$announcement_id = intval($_POST['announcement_id']);
$vote_type = $_POST['vote_type'];

// Kontrollo nëse përdoruesi ka votuar më parë
$query = "SELECT * FROM votes WHERE user_id = ? AND announcement_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $announcement_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Përditëso votën ekzistuese
    $query = "UPDATE votes SET vote_type = ? WHERE user_id = ? AND announcement_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $vote_type, $user_id, $announcement_id);
} else {
    // Shto një votë të re
    $query = "INSERT INTO votes (announcement_id, user_id, vote_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $announcement_id, $user_id, $vote_type);
}

$stmt->execute();
header("Location: ../back_End/announcements.php");
?>
