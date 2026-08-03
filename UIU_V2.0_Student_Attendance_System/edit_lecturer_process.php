<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$lecturer_id = $_POST['lecturer_id'];
$name = $_POST['name'];
$faculty_id = $_POST['faculty_id'];
$password = $_POST['password'];

if (!empty($password)) {
    // Password field was filled in - update it too
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE lecturer SET lecturer_name = ?, faculty_id = ?, lecturer_password = ? WHERE lecturer_id = ?");
    $stmt->bind_param("ssss", $name, $faculty_id, $hashed_password, $lecturer_id);
} else {
    // Password left blank - don't touch it
    $stmt = $conn->prepare("UPDATE lecturer SET lecturer_name = ?, faculty_id = ? WHERE lecturer_id = ?");
    $stmt->bind_param("sss", $name, $faculty_id, $lecturer_id);
}

$stmt->execute();

header("Location: admin_lecturer.php");
exit();
?>