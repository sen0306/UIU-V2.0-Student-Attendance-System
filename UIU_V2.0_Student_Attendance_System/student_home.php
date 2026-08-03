<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Get all courses this student is enrolled in
$stmt = $conn->prepare("SELECT course.course_id, course.course_name 
                        FROM student_course 
                        JOIN course ON student_course.course_id = course.course_id 
                        WHERE student_course.student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$courses_result = $stmt->get_result();

// Calculate percentage for each course, and track which ones are below 70%
$courses = [];
$low_attendance_courses = [];

while ($course = $courses_result->fetch_assoc()) {
    $stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM session WHERE course_id = ?");
    $stmt2->bind_param("s", $course['course_id']);
    $stmt2->execute();
    $total_sessions = $stmt2->get_result()->fetch_assoc()['total'];

    $stmt3 = $conn->prepare("SELECT COUNT(*) as attended FROM attendance_record 
                            WHERE student_id = ? AND status IN ('Attended', 'Absent with Excuse')
                            AND session_id IN (SELECT session_id FROM session WHERE course_id = ?)");
    $stmt3->bind_param("ss", $student_id, $course['course_id']);
    $stmt3->execute();
    $attended_count = $stmt3->get_result()->fetch_assoc()['attended'];

    $percentage = $total_sessions > 0 ? round(($attended_count / $total_sessions) * 100) : 0;

    $course['percentage'] = $percentage;
    $courses[] = $course;

    if ($percentage < 70) {
        $low_attendance_courses[] = $course;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="student_home.css">
    <title>UIU V2.0 Student Attendance System</title>

</head>
<body>

    <header>
    <div class="logo">UIU V2.0 Student Attendance System</div>
    </header>

    <main>
        <section>

        <?php if (!empty($low_attendance_courses)): ?>
            <div class="warning-banner">
                <h1>Warning: You have been barred! Please improve your attendance to meet the university's minimum requirement Minimum attendance: (70%).</h1>
            </div>
        <?php endif; ?>
        
        <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Actions</th>
                    <th>Attendance Percentage</th>
                    </tr>
            </thead>
            <tbody>
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <?php $is_barred = $course['percentage'] < 70; ?>
                        <tr>
                            <td><?php echo $course['course_id']; ?></td>
                            <td><?php echo $course['course_name']; ?><?php if ($is_barred) echo ' <span class="barred-label">(Barred)</span>'; ?></td>
                            <td><a href="student_course_sessions.php?id=<?php echo $course['course_id']; ?>" class="btn-view">View</a></td>
                            <td><?php echo $course['percentage']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty_message">No courses assigned yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>

            <div class="signout">
                <a href="logout.php" class="signout-button">Sign Out</a>
            </div>

    </section>
</main>

</body>
</html>