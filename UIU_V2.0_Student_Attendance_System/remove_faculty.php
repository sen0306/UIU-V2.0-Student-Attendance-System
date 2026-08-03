<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$faculty_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM faculty WHERE faculty_id = ?");
$stmt->bind_param("s", $faculty_id);

try {
    $stmt->execute();
    header("Location: admin_faculty.php");
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: admin_faculty.php?error=in_use");
    exit();
}
?>