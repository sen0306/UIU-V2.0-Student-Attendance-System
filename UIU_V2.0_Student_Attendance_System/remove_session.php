<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$session_id = $_GET['id'];

$lookup = $conn->prepare("SELECT course_id FROM session WHERE session_id = ?");
$lookup->bind_param("s", $session_id);
$lookup->execute();
$session_data = $lookup->get_result()->fetch_assoc();
$course_id = $session_data['course_id'];

$stmt = $conn->prepare("DELETE FROM session WHERE session_id = ?");
$stmt->bind_param("s", $session_id);

try {
    $stmt->execute();
    header("Location: admin_course_sessions.php?id=" . $course_id);
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: admin_course_sessions.php?id=" . $course_id . "&error=in_use");
    exit();
}
?>