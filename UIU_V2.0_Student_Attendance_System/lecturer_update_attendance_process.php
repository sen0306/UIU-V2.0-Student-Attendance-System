<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_id = $_SESSION['user_id'];
$session_id = $_GET['session_id'];
$student_id = $_GET['student_id'];
$status = $_GET['status'];

// Security check: confirm this session belongs to a course assigned to this lecturer
$check = $conn->prepare("SELECT session.session_id FROM session 
                          JOIN course ON session.course_id = course.course_id 
                          WHERE session.session_id = ? AND course.lecturer_id = ?");
$check->bind_param("ss", $session_id, $lecturer_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    header("Location: lecturer_home.php");
    exit();
}

// Check if an attendance record already exists for this student + session
$stmt = $conn->prepare("SELECT attendance_id FROM attendance_record WHERE session_id = ? AND student_id = ?");
$stmt->bind_param("ss", $session_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt2 = $conn->prepare("UPDATE attendance_record SET status = ? WHERE session_id = ? AND student_id = ?");
    $stmt2->bind_param("sss", $status, $session_id, $student_id);
    $stmt2->execute();
} else {
    $id_result = $conn->query("SELECT attendance_id FROM attendance_record ORDER BY attendance_id DESC LIMIT 1");
    if ($id_result->num_rows > 0) {
        $last_id = $id_result->fetch_assoc()['attendance_id'];
        $number = intval(substr($last_id, 2));
        $next_number = $number + 1;
    } else {
        $next_number = 1;
    }
    $new_id = "AT" . str_pad($next_number, 4, "0", STR_PAD_LEFT);

    $stmt2 = $conn->prepare("INSERT INTO attendance_record (attendance_id, session_id, student_id, status) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("ssss", $new_id, $session_id, $student_id, $status);
    $stmt2->execute();
}

header("Location: lecturer_session_update.php?id=" . $session_id);
exit();
?>