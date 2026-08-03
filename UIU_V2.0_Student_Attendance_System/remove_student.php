<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);

try {
    $stmt->execute();
    header("Location: admin_student.php");
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: admin_student.php?error=in_use");
    exit();
}
?>