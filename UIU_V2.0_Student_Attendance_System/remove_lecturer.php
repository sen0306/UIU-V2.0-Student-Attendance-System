<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$lecturer_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM lecturer WHERE lecturer_id = ?");
$stmt->bind_param("s", $lecturer_id);

try {
    $stmt->execute();
    header("Location: admin_lecturer.php");
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: admin_lecturer.php?error=in_use");
    exit();
}
?>