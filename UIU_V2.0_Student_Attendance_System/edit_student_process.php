<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$student_id = $_POST['student_id'];
$student_name = $_POST['student_name'];
$faculty_id = $_POST['faculty_id'];
$course_ids = isset($_POST['course_ids']) ? $_POST['course_ids'] : [];
$password = $_POST['password'];

if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE student SET student_name = ?, faculty_id = ?, student_password = ? WHERE student_id = ?");
    $stmt->bind_param("ssss", $student_name, $faculty_id, $hashed_password, $student_id);
} else {
    $stmt = $conn->prepare("UPDATE student SET student_name = ?, faculty_id = ? WHERE student_id = ?");
    $stmt->bind_param("sss", $student_name, $faculty_id, $student_id);
}
$stmt->execute();

// Remove all existing course links for this student, then re-add the currently checked ones
$stmt2 = $conn->prepare("DELETE FROM student_course WHERE student_id = ?");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();

foreach ($course_ids as $course_id) {
    $stmt3 = $conn->prepare("INSERT INTO student_course (student_id, course_id) VALUES (?, ?)");
    $stmt3->bind_param("ss", $student_id, $course_id);
    $stmt3->execute();
}

header("Location: admin_student.php");
exit();
?>