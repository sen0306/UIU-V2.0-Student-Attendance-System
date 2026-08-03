<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['course_id'];

$stmt = $conn->prepare("SELECT course_name FROM course WHERE course_id = ?");
$stmt->bind_param("s", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Session</title>
    <link rel="stylesheet" href="add_session.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <div class="left-arrow">
        <a href="admin_course_sessions.php?id=<?php echo $course_id; ?>"><i class="fa-solid fa-angle-left"></i></a>
        <p>Add Session - <?php echo $course['course_name']; ?></p>
    </div>

    <div class="form-box">
        <form action="add_session_process.php" method="post">

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message">Something went wrong. Please try again.</p>
            <?php endif; ?>

            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

            <label for="session_date">Date</label>
            <input type="date" name="session_date" id="session_date" required>

            <label for="session_time">Time</label>
            <input type="time" name="session_time" id="session_time" required>

            <button type="submit">Add Session</button>
        </form>
    </div>

</body>
</html>