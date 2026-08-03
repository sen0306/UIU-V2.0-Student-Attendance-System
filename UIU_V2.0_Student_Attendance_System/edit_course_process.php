<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_id = $_POST['course_id'];
$course_name = $_POST['course_name'];
$faculty_id = $_POST['faculty_id'];
$lecturer_id = !empty($_POST['lecturer_id']) ? $_POST['lecturer_id'] : NULL;

$stmt = $conn->prepare("UPDATE course SET course_name = ?, faculty_id = ?, lecturer_id = ? WHERE course_id = ?");
$stmt->bind_param("ssss", $course_name, $faculty_id, $lecturer_id, $course_id);
$stmt->execute();

header("Location: admin_course.php");
exit();
?>