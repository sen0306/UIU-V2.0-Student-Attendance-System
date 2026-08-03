<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_id = $_POST['course_id'];
$session_date = $_POST['session_date'];
$session_time = $_POST['session_time'];

// Auto-generate the next Session ID
$result = $conn->query("SELECT session_id FROM session ORDER BY session_id DESC LIMIT 1");

if ($result->num_rows > 0) {
    $last_id = $result->fetch_assoc()['session_id']; // e.g., "SE015"
    $number = intval(substr($last_id, 2)); // strips "SE"
    $next_number = $number + 1;
} else {
    $next_number = 1;
}

$new_id = "SE" . str_pad($next_number, 3, "0", STR_PAD_LEFT); // e.g., "SE016"

$stmt = $conn->prepare("INSERT INTO session (session_id, course_id, session_date, session_time) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $new_id, $course_id, $session_date, $session_time);

if ($stmt->execute()) {
    header("Location: admin_course_sessions.php?id=" . $course_id);
    exit();
} else {
    header("Location: add_session.php?course_id=" . $course_id . "&error=1");
    exit();
}
?>