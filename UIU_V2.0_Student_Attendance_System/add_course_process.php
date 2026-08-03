<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_name = $_POST['course_name'];
$faculty_id = $_POST['faculty_id'];
$lecturer_id = !empty($_POST['lecturer_id']) ? $_POST['lecturer_id'] : NULL;

// Auto-generate the next Course ID
$result = $conn->query("SELECT course_id FROM course ORDER BY course_id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $last_id = $result->fetch_assoc()['course_id']; // e.g., "C005"
    $number = intval(substr($last_id, 1));
    $next_number = $number + 1;
} else {
    $next_number = 1;
}

$new_id = "C" . str_pad($next_number, 3, "0", STR_PAD_LEFT); // e.g., "C006"

$stmt = $conn->prepare("INSERT INTO course (course_id, course_name, faculty_id, lecturer_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $new_id, $course_name, $faculty_id, $lecturer_id);

if ($stmt->execute()) {
    header("Location: admin_course.php");
    exit();
} else {
    header("Location: add_course.php?error=1");
    exit();
}
?>