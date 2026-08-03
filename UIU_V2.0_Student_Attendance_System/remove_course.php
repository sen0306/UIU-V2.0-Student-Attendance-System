<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM course WHERE course_id = ?");
$stmt->bind_param("s", $course_id);

try {
    $stmt->execute();
    header("Location: admin_course.php");
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: admin_course.php?error=in_use");
    exit();
}
?>