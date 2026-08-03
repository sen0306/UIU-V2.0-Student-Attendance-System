<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$student_name = $_POST['student_name'];
$faculty_id = $_POST['faculty_id'];
$course_ids = isset($_POST['course_ids']) ? $_POST['course_ids'] : [];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Auto-generate the next Student ID
$result = $conn->query("SELECT student_id FROM student ORDER BY student_id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $last_id = $result->fetch_assoc()['student_id'];
    $number = intval(substr($last_id, 1));
    $next_number = $number + 1;
} else {
    $next_number = 1;
}

$new_id = "S" . str_pad($next_number, 3, "0", STR_PAD_LEFT);

// Insert the student record
$stmt = $conn->prepare("INSERT INTO student (student_id, student_name, student_password, faculty_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $new_id, $student_name, $hashed_password, $faculty_id);

if ($stmt->execute()) {
    // Student created successfully - now insert their course enrollments
    foreach ($course_ids as $course_id) {
        $stmt2 = $conn->prepare("INSERT INTO student_course (student_id, course_id) VALUES (?, ?)");
        $stmt2->bind_param("ss", $new_id, $course_id);
        $stmt2->execute();
    }

    header("Location: admin_student.php");
    exit();
} else {
    header("Location: add_student.php?error=1");
    exit();
}
?>