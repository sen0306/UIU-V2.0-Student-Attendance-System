<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$name = $_POST['name'];
$faculty_id = $_POST['faculty_id'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$result = $conn->query("SELECT lecturer_id FROM lecturer ORDER BY lecturer_id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $last_id = $result->fetch_assoc()['lecturer_id'];
    $number = intval(substr($last_id, 1));
    $next_number = $number + 1;
} else {
    $next_number = 1; 
}

$new_id = "L" . str_pad($next_number, 3, "0", STR_PAD_LEFT);

$stmt = $conn->prepare("INSERT INTO lecturer (lecturer_id, lecturer_name, lecturer_password, faculty_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $new_id, $name, $hashed_password, $faculty_id);

if ($stmt->execute()) {
    header("Location: admin_lecturer.php");
    exit();
} else {
    header("Location: add_lecturer.php?error=1");
    exit();
}

?>