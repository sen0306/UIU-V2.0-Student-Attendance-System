<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$faculty_name = $_POST['faculty_name'];

// Auto-generate the next Faculty ID
$result = $conn->query("SELECT faculty_id FROM faculty ORDER BY faculty_id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $last_id = $result->fetch_assoc()['faculty_id']; // e.g., "F005"
    $number = intval(substr($last_id, 1));
    $next_number = $number + 1;
} else {
    $next_number = 1;
}

$new_id = "F" . str_pad($next_number, 3, "0", STR_PAD_LEFT); // e.g., "F006"

$stmt = $conn->prepare("INSERT INTO faculty (faculty_id, faculty_name) VALUES (?, ?)");
$stmt->bind_param("ss", $new_id, $faculty_name);

if ($stmt->execute()) {
    header("Location: admin_faculty.php");
    exit();
} else {
    header("Location: add_faculty.php?error=1");
    exit();
}
?>