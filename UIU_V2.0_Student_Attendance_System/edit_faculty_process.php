<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$faculty_id = $_POST['faculty_id'];
$faculty_name = $_POST['faculty_name'];

$stmt = $conn->prepare("UPDATE faculty SET faculty_name = ? WHERE faculty_id = ?");
$stmt->bind_param("ss", $faculty_name, $faculty_id);
$stmt->execute();

header("Location: admin_faculty.php");
exit();
?>